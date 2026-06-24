import pandas as pd
import numpy as np
import mysql.connector
from sklearn.svm import SVR
from sklearn.preprocessing import StandardScaler, RobustScaler
from sklearn.model_selection import TimeSeriesSplit
from sklearn.metrics import mean_absolute_error, mean_squared_error, r2_score
from sklearn.feature_selection import SelectKBest, f_regression
import joblib
from datetime import datetime
import warnings
warnings.filterwarnings('ignore')

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
# KONFIGURASI RESAMPLE
# =====================================================
# Pilihan interval resample:
# '1T' = 1 menit
# '5T' = 5 menit (REKOMENDASI)
# '15T' = 15 menit
# '30T' = 30 menit
# '1H' = 1 jam
RESAMPLE_RULE = '5min'  # Ubah sesuai kebutuhan

# Minimum sampel yang diperlukan setelah resample
MIN_SAMPLES_AFTER_RESAMPLE = 500

# =====================================================
# FUNGSI AMBIL DATA DENGAN VALIDASI
# =====================================================
def fetch_historical_data(days=90):
    conn = mysql.connector.connect(**DB_CONFIG)
    
    query = f"""
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
        WHERE timestamp >= DATE_SUB(NOW(), INTERVAL {days} DAY)
        AND pm2_5 IS NOT NULL
        AND temperature IS NOT NULL
        ORDER BY timestamp ASC
    """
    
    df = pd.read_sql(query, conn)
    conn.close()
    
    print(f"✓ Data berhasil diambil: {len(df)} record")
    
    # TAMPILKAN INFORMASI RENTANG WAKTU
    print("\n" + "="*60)
    print("INFORMASI RENTANG WAKTU DATA")
    print("="*60)
    print(f"Timestamp pertama: {df['timestamp'].min()}")
    print(f"Timestamp terakhir: {df['timestamp'].max()}")
    
    duration = df['timestamp'].max() - df['timestamp'].min()
    print(f"Total durasi data: {duration}")
    print(f"Rata-rata interval: {(duration.total_seconds() / len(df)):.2f} detik")
    
    return df

# =====================================================
# FUNGSI RESAMPLE DATA
# =====================================================
def resample_data(df, rule='1T'):
    """
    Resample data dari interval tinggi (per detik) ke interval yang lebih rendah
    
    Parameters:
    - df: DataFrame dengan kolom timestamp dan parameter sensor
    - rule: pandas resample rule
        '1T' = 1 menit
        '5T' = 5 menit
        '15T' = 15 menit
        '30T' = 30 menit
        '1H' = 1 jam
    
    Returns:
    - DataFrame yang telah di-resample
    """
    print("\n" + "="*60)
    print("RESAMPLE DATA")
    print("="*60)
    print(f"Rule resample: {rule}")
    
    df_resampled = df.copy()
    df_resampled.set_index('timestamp', inplace=True)
    
    # Hitung jumlah data sebelum resample
    before = len(df_resampled)
    
    # Lakukan resample dengan agregasi mean
    df_resampled = df_resampled.resample(rule).agg({
        'temperature': 'mean',
        'humidity': 'mean',
        'pm1_0': 'mean',
        'pm2_5': 'mean',
        'pm10': 'mean',
        'pollutant': 'mean',
        'ozone': 'mean',
        'no2': 'mean'
    })
    
    # Hapus baris yang memiliki NaN (interval tanpa data)
    df_resampled = df_resampled.dropna()
    
    # Reset index
    df_resampled = df_resampled.reset_index()
    
    after = len(df_resampled)
    
    print(f"\n📊 Hasil Resample:")
    print(f"   Data sebelum: {before} records")
    print(f"   Data setelah: {after} records")
    print(f"   Reduksi: {(1 - after/before)*100:.1f}%")
    
    # Hitung interval rata-rata setelah resample
    if len(df_resampled) > 1:
        interval = (df_resampled['timestamp'].iloc[-1] - df_resampled['timestamp'].iloc[0]).total_seconds() / len(df_resampled)
        print(f"   Interval rata-rata setelah: {interval:.0f} detik ({interval/60:.1f} menit)")
    
    # Peringatan jika data terlalu sedikit
    if after < MIN_SAMPLES_AFTER_RESAMPLE:
        print(f"\n⚠️ PERINGATAN: Data hanya {after} records!")
        print(f"   Minimal yang direkomendasikan: {MIN_SAMPLES_AFTER_RESAMPLE} records")
        print(f"   💡 Solusi: Gunakan rule yang lebih besar (misal '15T' atau '1H')")
    
    return df_resampled

# =====================================================
# FUNGSI DETEKSI KEPADATAN DATA
# =====================================================
def check_data_density(df):
    """
    Memeriksa kepadatan data dan memberikan rekomendasi interval resample
    """
    if len(df) < 2:
        return '1H'  # default
    
    # Hitung interval rata-rata antar timestamp
    time_diffs = df['timestamp'].diff().dt.total_seconds().dropna()
    avg_interval = time_diffs.mean()
    min_interval = time_diffs.min()
    max_interval = time_diffs.max()
    
    print("\n" + "="*60)
    print("ANALISIS KEPADATAN DATA")
    print("="*60)
    print(f"Interval rata-rata: {avg_interval:.2f} detik")
    print(f"Interval minimum: {min_interval:.2f} detik")
    print(f"Interval maksimum: {max_interval:.2f} detik")
    
    # Rekomendasi interval resample berdasarkan data
    if avg_interval < 10:  # data per detik atau sub-10 detik
        recommended = '5T'
        print("\n💡 Rekomendasi: Data sangat padat (per detik)")
        print(f"   Gunakan resample rule = '5T' (5 menit) atau lebih besar")
    elif avg_interval < 60:  # data per menit
        recommended = '15T'
        print("\n💡 Rekomendasi: Data cukup padat (per menit)")
        print(f"   Gunakan resample rule = '15T' (15 menit)")
    elif avg_interval < 300:  # data per 5 menit
        recommended = '1H'
        print("\n💡 Rekomendasi: Data normal (per 5 menitan)")
        print(f"   Resample tidak terlalu diperlukan, atau gunakan rule = '1H'")
    else:
        recommended = RESAMPLE_RULE
        print("\n💡 Rekomendasi: Data sudah jarang, resample mungkin tidak diperlukan")
    
    return recommended

# =====================================================
# DETEKSI DAN HAPUS OUTLIER
# =====================================================
def remove_outliers(df, target_col, threshold=3):
    """Menghapus outlier menggunakan metode IQR"""
    Q1 = df[target_col].quantile(0.25)
    Q3 = df[target_col].quantile(0.75)
    IQR = Q3 - Q1
    lower_bound = Q1 - threshold * IQR
    upper_bound = Q3 + threshold * IQR
    
    before = len(df)
    df_clean = df[(df[target_col] >= lower_bound) & (df[target_col] <= upper_bound)]
    after = len(df_clean)
    
    if before - after > 0:
        print(f"    Outlier {target_col}: dihapus {before - after} data ({((before-after)/before*100):.1f}%)")
    
    return df_clean

# =====================================================
# FUNGSI FEATURE ENGINEERING SEDERHANA
# =====================================================
def create_features_simple(df, target_col, lookback=3):
    """
    Membuat fitur untuk time series forecasting
    """
    df = df.copy()
    
    # Fitur waktu dasar
    df['hour'] = df['timestamp'].dt.hour
    df['day_of_week'] = df['timestamp'].dt.dayofweek
    df['hour_sin'] = np.sin(2 * np.pi * df['hour'] / 24)
    df['hour_cos'] = np.cos(2 * np.pi * df['hour'] / 24)
    
    # Lag features untuk target
    for lag in range(1, lookback + 1):
        df[f'{target_col}_lag_{lag}'] = df[target_col].shift(lag)
    
    # Lag untuk parameter pendukung (1 lag saja)
    supporting_features = ['temperature', 'humidity', 'pm2_5', 'ozone', 'no2']
    for feat in supporting_features:
        if feat != target_col and feat in df.columns:
            df[f'{feat}_lag_1'] = df[feat].shift(1)
    
    # Rolling mean
    df[f'{target_col}_rolling_mean_3'] = df[target_col].rolling(window=3).mean()
    
    # Hapus NaN
    df = df.dropna()
    
    # Pilih fitur yang tersedia
    feature_candidates = [
        'hour_sin', 'hour_cos',
        f'{target_col}_lag_1', f'{target_col}_lag_2', f'{target_col}_lag_3',
        f'{target_col}_rolling_mean_3'
    ]
    
    # Tambahkan lag pendukung
    for feat in supporting_features:
        if feat != target_col and f'{feat}_lag_1' in df.columns:
            feature_candidates.append(f'{feat}_lag_1')
    
    available_features = [f for f in feature_candidates if f in df.columns]
    X = df[available_features]
    y = df[target_col]
    
    print(f"    Fitur yang digunakan ({len(available_features)}): {available_features[:5]}...")
    
    return X, y

# =====================================================
# TRAINING SVR DENGAN VALIDASI
# =====================================================
def train_svr_for_target(df_original, target_name):
    """Melatih model SVR dengan validasi data terlebih dahulu"""
    print(f"\n{'='*50}")
    print(f"📊 Memproses target: {target_name.upper()}")
    print(f"{'='*50}")
    
    # 1. Cek apakah target tersedia
    if target_name not in df_original.columns:
        print(f"  ❌ Kolom '{target_name}' tidak ditemukan!")
        return None
    
    # 2. Cek varians data
    variance = df_original[target_name].var()
    if variance < 0.01:
        print(f"  ⚠️ Varians {target_name} terlalu kecil ({variance:.6f})")
        return {
            'type': 'constant',
            'mean_value': df_original[target_name].mean(),
            'unit': UNITS.get(target_name, '')
        }
    
    # 3. Hapus outlier
    df = remove_outliers(df_original, target_name, threshold=3)
    
    if len(df) < 50:
        print(f"  ❌ Data tidak cukup setelah hapus outlier ({len(df)} sampel)")
        return None
    
    # 4. Buat fitur
    X, y = create_features_simple(df, target_name, lookback=3)
    
    if len(X) < 30:
        print(f"  ❌ Data tidak cukup setelah feature engineering ({len(X)} sampel)")
        return None
    
    # 5. Normalisasi
    scaler_X = RobustScaler()
    scaler_y = RobustScaler()
    
    X_scaled = scaler_X.fit_transform(X)
    y_scaled = scaler_y.fit_transform(y.values.reshape(-1, 1)).ravel()
    
    # 6. Split data (80/20) - urutan waktu tetap
    split_index = int(len(X_scaled) * 0.8)
    X_train, X_test = X_scaled[:split_index], X_scaled[split_index:]
    y_train, y_test = y_scaled[:split_index], y_scaled[split_index:]
    
    # 7. Training
    print(f"    Training dengan {len(X_train)} sampel...")
    
    params_to_try = [
        {'C': 1, 'epsilon': 0.1, 'kernel': 'rbf', 'gamma': 'scale'},
        {'C': 5, 'epsilon': 0.1, 'kernel': 'rbf', 'gamma': 'scale'},
        {'C': 10, 'epsilon': 0.1, 'kernel': 'rbf', 'gamma': 'scale'},
    ]
    
    best_model = None
    best_mae = float('inf')
    
    for params in params_to_try:
        svr = SVR(**params)
        svr.fit(X_train, y_train)
        
        y_pred_scaled = svr.predict(X_test)
        y_pred = scaler_y.inverse_transform(y_pred_scaled.reshape(-1, 1)).ravel()
        y_test_actual = scaler_y.inverse_transform(y_test.reshape(-1, 1)).ravel()
        mae = mean_absolute_error(y_test_actual, y_pred)
        
        if mae < best_mae:
            best_mae = mae
            best_model = svr
            best_params = params
    
    # 8. Evaluasi final
    y_pred_scaled = best_model.predict(X_test)
    y_pred = scaler_y.inverse_transform(y_pred_scaled.reshape(-1, 1)).ravel()
    y_test_actual = scaler_y.inverse_transform(y_test.reshape(-1, 1)).ravel()
    
    mae = mean_absolute_error(y_test_actual, y_pred)
    rmse = np.sqrt(mean_squared_error(y_test_actual, y_pred))
    r2 = r2_score(y_test_actual, y_pred)
    
    print(f"\n    ✅ HASIL TRAINING {target_name.upper()}:")
    print(f"       MAE  : {mae:.3f} {UNITS.get(target_name, '')}")
    print(f"       RMSE : {rmse:.3f}")
    print(f"       R²   : {r2:.4f}")
    print(f"       Best params: C={best_params['C']}, epsilon={best_params['epsilon']}")
    
    if r2 < 0:
        print(f"    ⚠️ R² negatif, menggunakan prediksi baseline")
        return {
            'type': 'baseline',
            'mean_value': y.mean(),
            'unit': UNITS.get(target_name, '')
        }
    
    return {
        'type': 'svr',
        'model': best_model,
        'scaler_X': scaler_X,
        'scaler_y': scaler_y,
        'mae': mae,
        'rmse': rmse,
        'r2': r2,
        'target_name': target_name,
        'unit': UNITS.get(target_name, ''),
        'feature_names': X.columns.tolist()
    }

# =====================================================
# SIMPAN MODEL
# =====================================================
def save_all_models(models_dict, filename='models/model_svr.pkl'):
    import os
    os.makedirs('models', exist_ok=True)
    
    model_data = {
        'models': models_dict,
        'target_columns': TARGET_COLUMNS,
        'units': UNITS,
        'training_date': datetime.now().strftime('%Y-%m-%d %H:%M:%S'),
        'resample_rule': RESAMPLE_RULE,
        'n_models': len([m for m in models_dict.values() if m is not None])
    }
    
    joblib.dump(model_data, filename)
    print(f"\n✓ Model berhasil disimpan ke: {filename}")

# =====================================================
# FUNGSI UTAMA
# =====================================================
def main():
    print("="*70)
    print("TRAINING MULTI-MODEL SVR (DENGAN RESAMPLE DATA)")
    print("="*70)
    
    # 1. Ambil data dari database
    print("\n[1] Mengambil data dari database...")
    df_raw = fetch_historical_data(days=90)
    
    if len(df_raw) < 50:
        print("❌ Data tidak mencukupi. Minimal 50 record.")
        return
    
    # 2. Cek kepadatan data dan rekomendasi
    recommended_rule = check_data_density(df_raw)
    
    # 3. Tanyakan apakah ingin menggunakan resample (opsional, bisa di-skip jika data sudah bagus)
    use_resample = True  # Set ke False jika tidak ingin resample
    
    if use_resample:
        # 4. Resample data
        print(f"\n[2] Melakukan resample data dengan rule: {RESAMPLE_RULE}")
        df = resample_data(df_raw, RESAMPLE_RULE)
        
        if len(df) < MIN_SAMPLES_AFTER_RESAMPLE:
            print(f"\n⚠️ PERINGATAN: Data setelah resample hanya {len(df)} records!")
            print(f"   Minimal yang direkomendasikan: {MIN_SAMPLES_AFTER_RESAMPLE}")
            print(f"   Training tetap dilanjutkan, tetapi hasil mungkin kurang akurat.")
            
            # Tanyakan apakah tetap lanjut
            # Untuk otomatis, kita lanjutkan saja
    else:
        print("\n[2] Melewati resample data (menggunakan data asli)")
        df = df_raw
    
    # 5. Training untuk setiap target
    print("\n[3] Melatih model untuk setiap parameter...")
    
    models_dict = {}
    results_summary = []
    
    for target in TARGET_COLUMNS:
        result = train_svr_for_target(df, target)
        models_dict[target] = result
        
        if result:
            if result.get('type') == 'svr':
                results_summary.append({
                    'target': target,
                    'type': 'SVR',
                    'mae': result['mae'],
                    'rmse': result['rmse'],
                    'r2': result['r2'],
                    'unit': result['unit']
                })
            else:
                results_summary.append({
                    'target': target,
                    'type': result['type'],
                    'mae': result.get('mean_value', 0),
                    'rmse': 0,
                    'r2': 'baseline',
                    'unit': result['unit']
                })
    
    # 6. Simpan model
    print("\n[4] Menyimpan semua model...")
    save_all_models(models_dict)
    
    # 7. Ringkasan hasil
    print("\n" + "="*70)
    print("RINGKASAN HASIL TRAINING")
    print("="*70)
    print(f"{'Parameter':<15} {'Metode':<12} {'MAE':<15} {'RMSE':<15} {'R²/Satuan':<15}")
    print("-" * 72)
    
    for r in results_summary:
        if r['type'] == 'baseline':
            print(f"{r['target']:<15} {'BASELINE':<12} {r['mae']:<15.3f} {'-':<15} {'(mean value)':<15}")
        else:
            print(f"{r['target']:<15} {'SVR':<12} {r['mae']:<15.3f} {r['rmse']:<15.3f} {r['r2']:<15.4f}")
    
    print("\n" + "="*70)
    print("✅ TRAINING SELESAI!")
    print(f"📌 Resample rule yang digunakan: {RESAMPLE_RULE}")
    print("="*70)

if __name__ == "__main__":
    main()