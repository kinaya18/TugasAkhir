from flask import Flask, request, jsonify
from flask_cors import CORS
import joblib
import numpy as np
import pandas as pd
from datetime import datetime, timedelta
import mysql.connector
from mysql.connector import Error
import warnings
import os
import sys
import traceback
import pytz

warnings.filterwarnings('ignore')

app = Flask(__name__)
CORS(app)

# =====================================================
# KONFIGURASI
# =====================================================
DB_CONFIG = {
    'host': 'localhost',
    'user': 'meh',
    'password': '123',
    'database': 'asma_db',
    'port': 3306
}

TARGET_COLUMNS = [
    'temperature',
    'humidity', 
    'pm1_0',
    'pm2_5',
    'pm10',
    'pollutant',
    'ozone',
    'no2'
]

UNITS = {
    'temperature': '°C',
    'humidity': '%',
    'pm1_0': 'μg/m³',
    'pm2_5': 'μg/m³',
    'pm10': 'μg/m³',
    'pollutant': 'ppm',
    'ozone': 'ppm',
    'no2': 'ppm'
}

# =====================================================
# KONFIGURASI TIMEZONE
# =====================================================
TIMEZONE = pytz.timezone('Asia/Jakarta')  # WIB GMT+7

def get_current_time():
    """Dapatkan waktu saat ini dengan timezone WIB"""
    return datetime.now(TIMEZONE)

def get_timezone_offset():
    """Dapatkan offset timezone dalam jam"""
    now = get_current_time()
    offset = now.utcoffset()
    if offset:
        return int(offset.total_seconds() / 3600)
    return 7

# =====================================================
# LOAD MODEL
# =====================================================
print("Loading models...")
model_path = 'models/model_svr.pkl'

if not os.path.exists(model_path):
    print(f"❌ Model file not found at: {model_path}")
    print(f"   Current directory: {os.getcwd()}")
    sys.exit(1)

try:
    model_data = joblib.load(model_path)
    print(f"✓ Models loaded: {model_data['n_models']} models")
    print(f"  Training date: {model_data['training_date']}")
except Exception as e:
    print(f"❌ Error loading models: {e}")
    sys.exit(1)

# =====================================================
# FUNGSI KONEKSI DATABASE
# =====================================================
def get_db_connection():
    try:
        conn = mysql.connector.connect(**DB_CONFIG)
        return conn
    except Error as e:
        print(f"❌ Database connection error: {e}")
        return None

# =====================================================
# FUNGSI AMBIL DATA HISTORIS 3 BULAN
# =====================================================
def get_historical_data_3months(min_records=10):
    """
    Ambil data historis 3 bulan terakhir (90 hari)
    """
    conn = get_db_connection()
    if not conn:
        return None, "Gagal koneksi ke database"
    
    try:
        cursor = conn.cursor(dictionary=True)
        
        # Cek total data
        cursor.execute("SELECT COUNT(*) as total FROM data_udara")
        total = cursor.fetchone()
        print(f"📊 Total data di database: {total['total']} records")
        
        if total['total'] == 0:
            cursor.close()
            conn.close()
            return None, "Tabel data_udara kosong"
        
        # Cek data terbaru
        cursor.execute("SELECT MAX(timestamp) as latest, MIN(timestamp) as oldest FROM data_udara")
        date_range = cursor.fetchone()
        print(f"📅 Rentang data di database:")
        print(f"   Terlama: {date_range['oldest']}")
        print(f"   Terbaru: {date_range['latest']}")
        
        # Ambil data 3 bulan terakhir (90 hari)
        query = """
            SELECT 
                timestamp,
                temperature,
                humidity,
                pm1_0,
                pm2_5,
                pm10,
                pollutant,
                ozone,
                no2
            FROM data_udara
            WHERE timestamp >= DATE_SUB(NOW(), INTERVAL 90 DAY)
            AND temperature IS NOT NULL
            ORDER BY timestamp ASC
        """
        cursor.execute(query)
        results = cursor.fetchall()
        
        # Jika kurang dari min_records, ambil semua data
        if len(results) < min_records:
            print(f"⚠️ Data 3 bulan terakhir hanya {len(results)} record, mengambil semua data")
            query = """
                SELECT 
                    timestamp,
                    temperature,
                    humidity,
                    pm1_0,
                    pm2_5,
                    pm10,
                    pollutant,
                    ozone,
                    no2
                FROM data_udara
                WHERE temperature IS NOT NULL
                ORDER BY timestamp ASC
            """
            cursor.execute(query)
            results = cursor.fetchall()
        
        cursor.close()
        conn.close()
        
        if len(results) < min_records:
            return None, f"Data tidak cukup (hanya {len(results)} record, minimal {min_records})"
        
        df = pd.DataFrame(results)
        df['timestamp'] = pd.to_datetime(df['timestamp'])
        
        # Statistik data
        print(f"\n📊 Statistik data historis:")
        for col in ['temperature', 'humidity', 'pm2_5', 'pm10']:
            if col in df.columns:
                print(f"   {col}: min={df[col].min():.2f}, max={df[col].max():.2f}, mean={df[col].mean():.2f}")
        
        return df, None
        
    except Error as e:
        return None, f"Error database: {e}"
    except Exception as e:
        return None, f"Error: {e}"

# =====================================================
# FUNGSI PREDIKSI SATU LANGKAH
# =====================================================
def predict_next(df, target_col, model_info, next_timestamp):
    """
    Prediksi satu langkah ke depan dengan timestamp yang ditentukan
    """
    
    feature_names = model_info.get('feature_names', [])
    if not feature_names:
        return None
    
    latest = df.iloc[-1].copy()
    features = {}
    
    # Fitur waktu berdasarkan timestamp yang ditentukan
    hour = next_timestamp.hour
    features['hour_sin'] = np.sin(2 * np.pi * hour / 24)
    features['hour_cos'] = np.cos(2 * np.pi * hour / 24)
    
    # Lag features (gunakan data terakhir)
    for lag in range(1, 4):
        lag_key = f'{target_col}_lag_{lag}'
        if len(df) >= lag:
            features[lag_key] = df[target_col].iloc[-lag]
        else:
            features[lag_key] = latest[target_col]
    
    # Rolling mean
    if len(df) >= 3:
        features[f'{target_col}_rolling_mean_3'] = df[target_col].iloc[-3:].mean()
    else:
        features[f'{target_col}_rolling_mean_3'] = latest[target_col]
    
    # Supporting features (gunakan data terakhir)
    supporting_features = ['temperature', 'humidity', 'pm2_5', 'ozone', 'no2']
    for feat in supporting_features:
        if feat != target_col and feat in df.columns:
            lag_key = f'{feat}_lag_1'
            features[lag_key] = latest[feat]
    
    # Build feature vector
    feature_vector = []
    missing_features = []
    
    for fname in feature_names:
        if fname in features:
            feature_vector.append(features[fname])
        else:
            feature_vector.append(0)
            missing_features.append(fname)
    
    if missing_features and len(missing_features) > 0 and len(missing_features) < 5:
        print(f"⚠️ Missing features for {target_col}: {missing_features}")
    
    X = np.array(feature_vector).reshape(1, -1)
    
    try:
        scaler_X = model_info['scaler_X']
        X_scaled = scaler_X.transform(X)
        
        model = model_info['model']
        y_pred_scaled = model.predict(X_scaled)
        
        scaler_y = model_info['scaler_y']
        y_pred = scaler_y.inverse_transform(y_pred_scaled.reshape(-1, 1))[0][0]
        
        # Batasi nilai ke range yang realistis
        if target_col == 'temperature':
            y_pred = max(15, min(40, y_pred))
        elif target_col == 'humidity':
            y_pred = max(30, min(90, y_pred))
        elif target_col in ['pm1_0', 'pm2_5', 'pm10']:
            y_pred = max(0, min(200, y_pred))
        elif target_col in ['ozone', 'no2']:
            y_pred = max(0, min(0.1, y_pred))
        elif target_col == 'pollutant':
            y_pred = max(400, min(5000, y_pred))
        
        return float(round(y_pred, 3))
        
    except Exception as e:
        print(f"❌ Error in predict_next for {target_col}: {e}")
        return None

# =====================================================
# FUNGSI PREDIKSI MULTI-STEP DARI WAKTU SEKARANG
# =====================================================
def predict_future_from_now(days=7, interval_minutes=5):
    """
    Prediksi multi-step dari waktu sekarang menggunakan data 3 bulan terakhir
    """
    
    print(f"\n🔮 Starting {days} days prediction (interval {interval_minutes} minutes)")
    print(f"⏰ Timezone: WIB (GMT+7)")
    print("="*60)
    
    # 1. Ambil data historis 3 bulan terakhir
    df_hist, error = get_historical_data_3months(min_records=10)
    if error:
        return None, error
    
    if df_hist is None or len(df_hist) < 10:
        return None, "Data historis tidak mencukupi (minimal 10 record)"
    
    print(f"\n📈 Menggunakan {len(df_hist)} data historis dari 3 bulan terakhir")
    
    # 2. Gunakan waktu sekarang sebagai awal prediksi
    start_time = get_current_time()
    # Bulatkan ke interval terdekat
    minutes = start_time.minute
    rounded_minutes = (minutes // interval_minutes) * interval_minutes
    start_time = start_time.replace(minute=rounded_minutes, second=0, microsecond=0)
    
    # Jika waktu sekarang sudah melewati interval, tambahkan 1 interval
    if minutes % interval_minutes != 0:
        start_time = start_time + timedelta(minutes=interval_minutes)
    
    print(f"\n🕐 Waktu sekarang: {get_current_time().strftime('%Y-%m-%d %H:%M:%S WIB')}")
    print(f"📅 Mulai prediksi dari: {start_time.strftime('%Y-%m-%d %H:%M:%S WIB')}")
    
    # 3. Hitung total langkah prediksi
    total_steps = int(days * 24 * 60 / interval_minutes)
    print(f"✓ Total prediction steps: {total_steps}")
    
    # 4. Inisialisasi dataframe untuk prediksi
    df_future = df_hist.copy()
    
    # 5. Prediksi recursive dari waktu sekarang
    predictions = []
    timestamps = []
    
    # Mulai dari waktu sekarang
    current_time = start_time
    
    for step in range(total_steps):
        # Hitung timestamp berikutnya
        next_timestamp = current_time + timedelta(minutes=interval_minutes * (step + 1))
        
        # Prediksi untuk setiap parameter
        pred_row = {'timestamp': next_timestamp}
        
        for target in TARGET_COLUMNS:
            model_info = model_data['models'].get(target)
            
            if not model_info:
                pred_row[target] = 0
                continue
                
            if model_info.get('type') in ['constant', 'baseline']:
                pred_row[target] = float(model_info.get('mean_value', 0))
                continue
            
            try:
                # Prediksi satu langkah dengan timestamp yang ditentukan
                pred_value = predict_next(df_future, target, model_info, next_timestamp)
                pred_row[target] = pred_value if pred_value is not None else 0
                
            except Exception as e:
                print(f"❌ Error predicting {target} at step {step}: {e}")
                pred_row[target] = 0
        
        # Tambahkan ke dataframe future
        df_future = pd.concat([df_future, pd.DataFrame([pred_row])], ignore_index=True)
        
        # Simpan prediksi
        predictions.append(pred_row)
        timestamps.append(next_timestamp)
        
        # Progress indicator
        if step % 100 == 0:
            progress = ((step + 1) / total_steps * 100)
            print(f"  Progress: {step+1}/{total_steps} steps ({progress:.1f}%)")
    
    print(f"\n✓ Prediction completed! {len(predictions)} predictions generated")
    print(f"  From: {timestamps[0].strftime('%Y-%m-%d %H:%M:%S WIB')}")
    print(f"  To: {timestamps[-1].strftime('%Y-%m-%d %H:%M:%S WIB')}")
    
    # 6. Konversi ke DataFrame
    df_predictions = pd.DataFrame(predictions)
    
    return df_predictions, None

# =====================================================
# FUNGSI SIMPAN PREDIKSI
# =====================================================
def save_future_predictions(df_predictions, days=7, interval_minutes=5):
    """Simpan prediksi future ke database"""
    
    conn = get_db_connection()
    if not conn:
        return False, "Gagal koneksi ke database"
    
    try:
        cursor = conn.cursor()
        
        # Buat tabel jika belum ada
        cursor.execute("""
            CREATE TABLE IF NOT EXISTS data_udara_prediksi_future (
                id INT AUTO_INCREMENT PRIMARY KEY,
                timestamp DATETIME,
                temperature FLOAT,
                humidity FLOAT,
                pm1_0 FLOAT,
                pm2_5 FLOAT,
                pm10 FLOAT,
                pollutant FLOAT,
                ozone FLOAT,
                no2 FLOAT,
                prediction_time DATETIME DEFAULT CURRENT_TIMESTAMP,
                days_ahead INT DEFAULT 7,
                interval_minutes INT DEFAULT 5,
                model_type VARCHAR(50) DEFAULT 'svr',
                prediction_start DATETIME
            )
        """)
        
        # Insert data
        insert_query = """
            INSERT INTO data_udara_prediksi_future (
                timestamp, temperature, humidity, pm1_0, pm2_5, pm10,
                pollutant, ozone, no2, prediction_time, days_ahead, 
                interval_minutes, prediction_start
            ) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s)
        """
        
        now_wib = get_current_time()
        now_str = now_wib.strftime('%Y-%m-%d %H:%M:%S')
        start_time = df_predictions['timestamp'].iloc[0].strftime('%Y-%m-%d %H:%M:%S')
        
        batch_size = 1000
        total_inserted = 0
        
        for i in range(0, len(df_predictions), batch_size):
            batch = df_predictions.iloc[i:i+batch_size]
            values = []
            
            for _, row in batch.iterrows():
                ts = row['timestamp']
                if isinstance(ts, pd.Timestamp):
                    ts_str = ts.strftime('%Y-%m-%d %H:%M:%S')
                else:
                    ts_str = str(ts)
                
                values.append((
                    ts_str,
                    float(row.get('temperature', 0)),
                    float(row.get('humidity', 0)),
                    float(row.get('pm1_0', 0)),
                    float(row.get('pm2_5', 0)),
                    float(row.get('pm10', 0)),
                    float(row.get('pollutant', 0)),
                    float(row.get('ozone', 0)),
                    float(row.get('no2', 0)),
                    now_str,
                    days,
                    interval_minutes,
                    start_time
                ))
            
            cursor.executemany(insert_query, values)
            conn.commit()
            total_inserted += len(values)
            print(f"  Saved batch {i//batch_size + 1}: {len(values)} records")
        
        cursor.close()
        conn.close()
        
        print(f"✓ Total {total_inserted} predictions saved to database")
        print(f"  Historical data used: 90 days (3 months)")
        return True, f"{total_inserted} records saved"
        
    except Error as e:
        print(f"❌ Save error: {e}")
        return False, str(e)

# =====================================================
# FUNGSI GENERATE SUMMARY
# =====================================================
def generate_summary(df_predictions):
    """Generate summary statistics dari prediksi"""
    
    summary = {
        'total_predictions': len(df_predictions),
        'timezone': 'WIB (GMT+7)',
        'historical_days': 90,
        'time_range': {
            'start': df_predictions['timestamp'].iloc[0].strftime('%Y-%m-%d %H:%M:%S WIB'),
            'end': df_predictions['timestamp'].iloc[-1].strftime('%Y-%m-%d %H:%M:%S WIB')
        }
    }
    
    # Statistik per parameter
    for col in TARGET_COLUMNS:
        if col in df_predictions.columns:
            data = df_predictions[col].dropna()
            if len(data) > 0:
                summary[col] = {
                    'min': float(data.min()),
                    'max': float(data.max()),
                    'mean': float(data.mean()),
                    'median': float(data.median()),
                    'std': float(data.std()),
                    'unit': UNITS.get(col, '')
                }
    
    return summary

# =====================================================
# ENDPOINT API
# =====================================================
@app.route('/predict/future', methods=['GET'])
def predict_future_endpoint():
    """Endpoint untuk prediksi future dari waktu sekarang"""
    
    print("\n" + "="*60)
    print("🔮 FUTURE PREDICTION REQUEST")
    print("="*60)
    
    try:
        # Ambil parameter
        days = request.args.get('days', 7, type=int)  # Default 7 hari
        interval = request.args.get('interval', 5, type=int)
        
        # Validasi
        if days < 1 or days > 30:
            return jsonify({
                'status': 'error',
                'message': 'Days harus antara 1-30'
            }), 400
            
        if interval < 1 or interval > 60:
            return jsonify({
                'status': 'error',
                'message': 'Interval harus antara 1-60 menit'
            }), 400
        
        print(f"📋 Parameter:")
        print(f"   Days: {days}")
        print(f"   Interval: {interval} minutes")
        print(f"   Historical: 90 days (3 months)")
        
        # Lakukan prediksi dari waktu sekarang
        df_predictions, error = predict_future_from_now(days, interval)
        
        if error:
            return jsonify({
                'status': 'error',
                'message': error
            }), 500
        
        if df_predictions is None or len(df_predictions) == 0:
            return jsonify({
                'status': 'error',
                'message': 'Gagal membuat prediksi'
            }), 500
        
        # Simpan ke database
        saved, save_msg = save_future_predictions(df_predictions, days, interval)
        
        if not saved:
            return jsonify({
                'status': 'warning',
                'message': 'Prediksi berhasil tetapi gagal simpan',
                'error': save_msg,
                'data': df_predictions.head(10).to_dict('records')
            }), 200
        
        # Generate summary
        summary = generate_summary(df_predictions)
        
        # Convert untuk response
        response_data = df_predictions.head(100).to_dict('records')
        
        # Format timestamp ke WIB
        for row in response_data:
            if 'timestamp' in row:
                ts = row['timestamp']
                if isinstance(ts, pd.Timestamp):
                    row['timestamp'] = ts.strftime('%Y-%m-%d %H:%M:%S WIB')
                else:
                    row['timestamp'] = str(ts)
        
        now_wib = get_current_time()
        
        return jsonify({
            'status': 'success',
            'message': f'Prediksi {days} hari berhasil menggunakan data 3 bulan terakhir',
            'timezone': 'WIB (GMT+7)',
            'current_time': now_wib.strftime('%Y-%m-%d %H:%M:%S WIB'),
            'historical_days': 90,
            'prediction_start': df_predictions['timestamp'].iloc[0].strftime('%Y-%m-%d %H:%M:%S WIB'),
            'prediction_end': df_predictions['timestamp'].iloc[-1].strftime('%Y-%m-%d %H:%M:%S WIB'),
            'summary': summary,
            'sample_data': response_data,
            'total_records': len(df_predictions),
            'parameters': TARGET_COLUMNS,
            'units': UNITS,
            'timestamp': now_wib.strftime('%Y-%m-%d %H:%M:%S')
        }), 200
        
    except Exception as e:
        print(f"❌ Error: {e}")
        traceback.print_exc()
        return jsonify({
            'status': 'error',
            'message': str(e)
        }), 500

@app.route('/predict/future/latest', methods=['GET'])
def get_latest_future_prediction():
    """Ambil prediksi future terbaru"""
    
    try:
        conn = get_db_connection()
        if not conn:
            return jsonify({
                'status': 'error',
                'message': 'Gagal koneksi database'
            }), 500
        
        cursor = conn.cursor(dictionary=True)
        
        query = """
            SELECT * FROM data_udara_prediksi_future
            ORDER BY prediction_time DESC, timestamp ASC
            LIMIT 100
        """
        
        cursor.execute(query)
        results = cursor.fetchall()
        cursor.close()
        conn.close()
        
        return jsonify({
            'status': 'success',
            'count': len(results),
            'data': results
        }), 200
        
    except Exception as e:
        return jsonify({
            'status': 'error',
            'message': str(e)
        }), 500

@app.route('/predict/future/stats', methods=['GET'])
def get_future_stats():
    """Ambil statistik prediksi future"""
    
    try:
        conn = get_db_connection()
        if not conn:
            return jsonify({
                'status': 'error',
                'message': 'Gagal koneksi database'
            }), 500
        
        cursor = conn.cursor(dictionary=True)
        
        stats_query = """
            SELECT 
                COUNT(*) as total,
                AVG(temperature) as avg_temp,
                MIN(temperature) as min_temp,
                MAX(temperature) as max_temp,
                AVG(humidity) as avg_humidity,
                MIN(humidity) as min_humidity,
                MAX(humidity) as max_humidity,
                AVG(pm2_5) as avg_pm2_5,
                MIN(pm2_5) as min_pm2_5,
                MAX(pm2_5) as max_pm2_5,
                AVG(pm10) as avg_pm10,
                MIN(pm10) as min_pm10,
                MAX(pm10) as max_pm10,
                AVG(ozone) as avg_ozone,
                MIN(ozone) as min_ozone,
                MAX(ozone) as max_ozone,
                AVG(no2) as avg_no2,
                MIN(no2) as min_no2,
                MAX(no2) as max_no2,
                AVG(historical_days) as avg_historical_days
            FROM data_udara_prediksi_future
            WHERE prediction_time >= DATE_SUB(NOW(), INTERVAL 1 DAY)
        """
        
        cursor.execute(stats_query)
        stats = cursor.fetchone()
        cursor.close()
        conn.close()
        
        return jsonify({
            'status': 'success',
            'statistics': stats
        }), 200
        
    except Exception as e:
        return jsonify({
            'status': 'error',
            'message': str(e)
        }), 500

@app.route('/predict/future/historical-data', methods=['GET'])
def get_historical_data_info():
    """Informasi tentang data historis yang digunakan"""
    
    try:
        conn = get_db_connection()
        if not conn:
            return jsonify({
                'status': 'error',
                'message': 'Gagal koneksi database'
            }), 500
        
        cursor = conn.cursor(dictionary=True)
        
        query = """
            SELECT 
                COUNT(*) as total_records,
                MIN(timestamp) as oldest_data,
                MAX(timestamp) as latest_data,
                COUNT(DISTINCT DATE(timestamp)) as days_count
            FROM data_udara
            WHERE timestamp >= DATE_SUB(NOW(), INTERVAL 90 DAY)
        """
        
        cursor.execute(query)
        result = cursor.fetchone()
        cursor.close()
        conn.close()
        
        return jsonify({
            'status': 'success',
            'historical_days': 90,
            'data': result
        }), 200
        
    except Exception as e:
        return jsonify({
            'status': 'error',
            'message': str(e)
        }), 500

@app.route('/health', methods=['GET'])
def health():
    now_wib = get_current_time()
    return jsonify({
        'status': 'healthy',
        'models': model_data.get('n_models', 0),
        'training_date': model_data.get('training_date'),
        'timezone': 'WIB (GMT+7)',
        'historical_days': 90,
        'current_time': now_wib.strftime('%Y-%m-%d %H:%M:%S WIB'),
        'timestamp': now_wib.strftime('%Y-%m-%d %H:%M:%S')
    })

@app.route('/timezone', methods=['GET'])
def get_timezone_info():
    now_wib = get_current_time()
    return jsonify({
        'timezone': 'Asia/Jakarta',
        'display': 'WIB (GMT+7)',
        'current_time': now_wib.strftime('%Y-%m-%d %H:%M:%S WIB'),
        'utc_time': datetime.now(pytz.UTC).strftime('%Y-%m-%d %H:%M:%S UTC')
    })

if __name__ == '__main__':
    print("\n" + "="*60)
    print("🚀 STARTING FUTURE PREDICTION API")
    print("="*60)
    print(f"⏰ Timezone: WIB (GMT+7)")
    print(f"🕐 Current time: {get_current_time().strftime('%Y-%m-%d %H:%M:%S WIB')}")
    print(f"📊 Models loaded: {model_data.get('n_models', 0)}")
    print(f"📅 Historical data: 90 days (3 months)")
    print("\n📌 Endpoints:")
    print("   - http://localhost:5000/predict/future?days=7&interval=5")
    print("   - http://localhost:5000/predict/future/latest")
    print("   - http://localhost:5000/predict/future/stats")
    print("   - http://localhost:5000/predict/future/historical-data")
    print("   - http://localhost:5000/health")
    print("   - http://localhost:5000/timezone")
    print("="*60 + "\n")
    
    app.run(host='0.0.0.0', port=5000, debug=True)