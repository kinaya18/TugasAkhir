#include <DHT.h>
#include <WiFi.h>
#include "time.h"
#include <HTTPClient.h>
#include <MQUnifiedsensor.h>
// #include <TinyGPSPlus.h>      // Library GPS by Mikal Hart

// ========== WiFi CONFIGURATION ==========
const char* ssid = "EagleWings";
const char* password = "mintaksekarajo";

// 📌 String untuk zona waktu Asia/Jakarta (WIB)
const char* time_zone = "WIB-7";  // Format POSIX untuk GMT+7

const char* ntpServer = "pool.ntp.org";

// ========== SERVER CONFIGURATION ==========
const char* serverUrl = "http://192.168.43.55:8080/api/insert_data";

// ========== PIN DEFINITIONS ==========
#define DHTPIN 4
#define DHTTYPE DHT11
#define MQ135_PIN 34
#define MQ131_PIN 32
#define SEN0574_PIN 33

// PMS5003 using Serial2 ESP32
#define RXD2 16
#define TXD2 17

// ========== KONSTANTA ADC ==========
const float V_REF = 3.3;           // Tegangan referensi ESP32 (Volt)
const float ADC_MAX = 4095.0;      // Resolusi ADC ESP32 (12-bit)

// ========== KONFIGURASI MQ135 (Library MQUnifiedsensor) ==========
#define Board ("ESP-32")
#define MQ135_ADC_Bit_Resolution (12)
#define RatioMQ135CleanAir (3.6)   // RS/R0 untuk MQ135 di udara bersih

MQUnifiedsensor MQ135(Board, V_REF, MQ135_ADC_Bit_Resolution, MQ135_PIN, "MQ-135");

// ========== KONFIGURASI MQ131 (Library MQUnifiedsensor) ==========
#define RatioMQ131CleanAir (700.0)   // RS/R0 untuk MQ131 di udara bersih (Low Concentration)
// Koefisien untuk Ozone (O3) - Low Concentration version
const float MQ131_A = 23.943;       // Nilai A untuk rumus ppm = a * (Rs/R0)^b
const float MQ131_B = -1.11;        // Nilai B (exponent)

MQUnifiedsensor MQ131(Board, V_REF, MQ135_ADC_Bit_Resolution, MQ131_PIN, "MQ-131");

// ========== SEN0574 Variables (NO2) ==========
float no2 = 0;
float lastNo2 = 0;
float no2_voltage = 0;
float no2_v0_calibrated = 0.5;
const float NO2_V_REF = 3.3;
const float NO2_ADC_MAX = 4095.0;
const float NO2_V0 = 0.5;
const float NO2_SENSITIVITY = 0.5;

const int numReadingsNO2 = 10;
int no2_readings[10];
int no2_readIndex = 0;
int no2_total = 0;
int no2_average = 0;

// ========== DHT Variables ==========
float lastTemperature = 0;
float lastHumidity = 0;
float lastOzone = 0;

// ========== PMS5003 Variables ==========
struct PMS5003Data {
  uint16_t pm1_0;
  uint16_t pm2_5;
  uint16_t pm10;
} pmsData, lastPmsData;

// ========== THRESHOLD PERUBAHAN ==========
const float TEMP_THRESHOLD = 0.5;
const float HUMIDITY_THRESHOLD = 2.0;
const int PM10_THRESHOLD = 10;
const int PM25_THRESHOLD = 5;
const int PM1_THRESHOLD = 5;
const float POLLUTANT_THRESHOLD = 10.0;
const float OZONE_THRESHOLD = 0.005;
const float NO2_THRESHOLD = 0.005;

// ========== SEND INTERVAL ==========
const unsigned long MIN_SEND_INTERVAL = 10000;
unsigned long lastSendTime = 0;
const unsigned long FORCE_SEND_INTERVAL = 60000;
unsigned long lastForceSendTime = 0;

// ========== OBJECTS ==========
DHT dht(DHTPIN, DHTTYPE);
bool firstRun = true;

void setup() {
  Serial.begin(115200);
  delay(1000);
  
  Serial.println("ESP32 Air Quality Monitor Starting...");
  Serial.println("==========================================");
  Serial.println("Sensor yang aktif:");
  Serial.println("- DHT11 (Suhu & Kelembaban)");
  Serial.println("- PMS5003 (PM1.0, PM2.5, PM10)");
  Serial.println("- MQ135 (CO2 / Polutan Umum) - Library MQUnifiedsensor");
  Serial.println("- MQ131 (Ozone / O3) - Library MQUnifiedsensor");
  Serial.println("- SEN0574 (NO2 / Nitrogen Dioksida)");
  Serial.println("==========================================\n");

  // 🔧 KONFIGURASI WAKTU dengan Timezone String
  configTzTime(time_zone, ntpServer);

  // Inisialisasi DHT
  dht.begin();
  
  // Inisialisasi PMS5003
  Serial2.begin(9600, SERIAL_8N1, RXD2, TXD2);
  while(Serial2.available()) Serial2.read();
  
  // Initialize SEN0574 smoothing array
  for (int i = 0; i < numReadingsNO2; i++) {   
    no2_readings[i] = 0;                       
  }
  
  analogReadResolution(12);
  
  connectToWiFi();
  initMQ135();
  initMQ131();  // Inisialisasi MQ131 dengan library yang sama
  calibrateSEN0574();
  
  // Inisialisasi last values
  lastTemperature = dht.readTemperature();
  lastHumidity = dht.readHumidity();
  lastPmsData = pmsData;
  lastForceSendTime = millis();
  
  delay(2000);
  Serial.println("System Ready!");
  
  // Kirim data pertama kali
  float initialCO2 = 400.0;
  sendToServer(lastTemperature, lastHumidity, initialCO2, 0);
  lastSendTime = millis();
}

// ========== INISIALISASI MQ135 ==========
void initMQ135() {
  Serial.println("==========================================");
  Serial.println("INITIALIZING MQ-135 (Library MQUnifiedsensor)");
  Serial.println("==========================================");
  
  MQ135.setRegressionMethod(1);  // 1 = Exponential regression
  MQ135.setA(110.47);            // Nilai A untuk CO2/Polutan
  MQ135.setB(-2.862);            // Nilai B untuk CO2/Polutan
  MQ135.init();
  
  Serial.print("Kalibrasi MQ-135 di udara bersih");
  float calcR0 = 0;
  
  for (int i = 1; i <= 10; i++) {
    MQ135.update();
    calcR0 += MQ135.calibrate(RatioMQ135CleanAir);
    Serial.print(".");
    delay(500);
  }
  
  MQ135.setR0(calcR0 / 10);
  Serial.println(" selesai!");
  
  float r0Value = MQ135.getR0();
  if (isinf(r0Value)) {
    Serial.println("WARNING: Koneksi bermasalah, R0 infinite! Periksa wiring sensor.");
  }
  
  Serial.printf("R0 MQ-135 = %.2f\n", r0Value);
  Serial.println("==========================================\n");
}

// ========== INISIALISASI MQ131 dengan MQUnifiedsensor ==========
void initMQ131() {
  Serial.println("==========================================");
  Serial.println("INITIALIZING MQ-131 (OZONE SENSOR)");
  Serial.println("Menggunakan Library MQUnifiedsensor");
  Serial.println("Pastikan sensor berada di UDARA BERSIH!");
  Serial.println("==========================================");
  
  // Set metode regresi (1 = Exponential)
  MQ131.setRegressionMethod(1);
  
  // Set koefisien untuk Ozone (O3) - Low Concentration
  MQ131.setA(MQ131_A);
  MQ131.setB(MQ131_B);
  
  // Inisialisasi sensor
  MQ131.init();
  
  // Kalibrasi R0 di udara bersih
  Serial.print("Kalibrasi MQ-131 di udara bersih");
  float calcR0 = 0;
  
  for (int i = 1; i <= 10; i++) {
    MQ131.update();
    calcR0 += MQ131.calibrate(RatioMQ131CleanAir);
    Serial.print(".");
    delay(500);
  }
  
  MQ131.setR0(calcR0 / 10);
  Serial.println(" selesai!");
  
  float r0Value = MQ131.getR0();
  if (isinf(r0Value) || r0Value <= 0) {
    Serial.println("WARNING: R0 tidak valid! Periksa wiring sensor MQ131.");
    MQ131.setR0(30.0);  // Nilai default jika gagal
    Serial.println("Menggunakan nilai R0 default: 30.0");
  }
  
  Serial.printf("MQ-131 R0 = %.2f\n", MQ131.getR0());
  Serial.println("==========================================\n");
}

// ========== BACA MQ131 dengan MQUnifiedsensor ==========
float readMQ131_Ozone() {
  // Update data dari sensor
  MQ131.update();
  
  // Baca konsentrasi ozone dalam ppm
  float ozone = MQ131.readSensor();
  
  // Validasi nilai
  if (ozone < 0) ozone = 0;
  if (ozone > 2.0) ozone = 2.0;  // Batas untuk low concentration (0-1 ppm typical)
  
  return ozone;
}

// ========== BACA MQ135 ==========
float readMQ135_PPM() {
  MQ135.update();
  float co2ppm = MQ135.readSensor();
  float co2WithOffset = co2ppm + 400.0;
  
  if (co2WithOffset < 0) co2WithOffset = 0;
  if (co2WithOffset > 5000) co2WithOffset = 5000;
  
  return co2WithOffset;
}

// ========== BACA SEN0574 ==========
void readSEN0574() {
  no2_total = no2_total - no2_readings[no2_readIndex];
  no2_readings[no2_readIndex] = analogRead(SEN0574_PIN);
  no2_total = no2_total + no2_readings[no2_readIndex];
  no2_readIndex = (no2_readIndex + 1) % numReadingsNO2;
  no2_average = no2_total / numReadingsNO2;
  
  no2_voltage = no2_average * (NO2_V_REF / NO2_ADC_MAX);
  

  if (no2_voltage <= no2_v0_calibrated) {
    no2 = 0.001;
  } else {
    no2 = (no2_voltage - no2_v0_calibrated) / NO2_SENSITIVITY;
  }
  
  // Terapkan filter kalman sederhana untuk smoothing
  static float filteredNo2 = 0;
  const float alpha = 0.3;  // Faktor smoothing
  filteredNo2 = alpha * no2 + (1 - alpha) * filteredNo2;
  no2 = filteredNo2;
  
  if (no2 <= 0) no2 = 0.001;
  if (no2 > 5.0) no2 = 5.0;
}

// ========== BACA PMS5003 ==========
// ========== BACA PMS5003 YANG LEBIH ROBUST ==========
void readPMS5003() {
  static uint8_t buffer[32];
  static uint8_t bufferIndex = 0;
  
  // Baca semua data yang tersedia
  while (Serial2.available()) {
    uint8_t incomingByte = Serial2.read();
    
    // Cari frame header (0x42 0x4D)
    if (bufferIndex == 0 && incomingByte == 0x42) {
      buffer[bufferIndex++] = incomingByte;
    } 
    else if (bufferIndex == 1 && incomingByte == 0x4D) {
      buffer[bufferIndex++] = incomingByte;
    }
    else if (bufferIndex > 1) {
      buffer[bufferIndex++] = incomingByte;
      
      // Jika sudah mengumpulkan 32 byte, proses frame
      if (bufferIndex >= 32) {
        // Verifikasi checksum
        uint16_t checksum = 0;
        for (int i = 0; i < 30; i++) {
          checksum += buffer[i];
        }
        
        uint16_t receivedChecksum = (buffer[30] << 8) | buffer[31];
        
        if (checksum == receivedChecksum) {
          // Data valid
          uint16_t new_pm1_0 = (buffer[10] << 8) | buffer[11];
          uint16_t new_pm2_5 = (buffer[12] << 8) | buffer[13];
          uint16_t new_pm10 = (buffer[14] << 8) | buffer[15];
          
          // Filter nilai yang tidak masuk akal
          if (new_pm1_0 <= 1000 && new_pm2_5 <= 1000 && new_pm10 <= 1000) {
            pmsData.pm1_0 = new_pm1_0;
            pmsData.pm2_5 = new_pm2_5;
            pmsData.pm10 = new_pm10;
          }
        } else {
          Serial.println("PMS5003 Checksum Error");
          // Tampilkan header untuk debugging
          Serial.printf("Header: %02X %02X\n", buffer[0], buffer[1]);
        }
        
        // Reset buffer untuk frame berikutnya
        bufferIndex = 0;
      }
    }
    else {
      // Bukan header, reset
      bufferIndex = 0;
    }
  }
}

// ========== KALIBRASI SEN0574 ==========
void calibrateSEN0574() {
  Serial.println("==========================================");
  Serial.println("CALIBRATING SEN0574 (NO2 SENSOR)");
  Serial.println("Pastikan sensor di UDARA BERSIH!");
  Serial.println("==========================================");
  
  float totalVoltage = 0;
  int sampleCount = 100;
  
  for (int i = 0; i < sampleCount; i++) {
    int rawADC = analogRead(SEN0574_PIN);
    float voltage = rawADC * (NO2_V_REF / NO2_ADC_MAX);
    totalVoltage += voltage;
    delay(50);
  }
  
  float avgVoltage = totalVoltage / sampleCount;
  
  // PERBAIKI: Gunakan tegangan terukur sebagai baseline baru
  // JANGAN paksakan 0.5V jika tidak sesuai
  const float MEASURED_V0 = avgVoltage;  // Gunakan nilai terukur
  
  Serial.printf("Tegangan baseline terukur: %.3f V\n", avgVoltage);
  Serial.printf("Menggunakan V0 = %.3f V untuk perhitungan\n", MEASURED_V0);
  
  // Simpan nilai V0 untuk digunakan di readSEN0574()
  // Anda perlu membuat variabel global: float no2_v0_calibrated;
  no2_v0_calibrated = MEASURED_V0;
  
  Serial.println("==========================================");
}

// ========== LOOP UTAMA ==========
void loop() {
  readPMS5003();
  while (Serial2.available()) {
    readPMS5003();  // Baca selama ada data
  }

  // Baca sensor lainnya
  float humidity = dht.readHumidity();
  float temperature = dht.readTemperature();
  
  readPMS5003();
  float co2ppm = readMQ135_PPM();
  float ozone = readMQ131_Ozone();  // Gunakan fungsi baru
  readSEN0574();
  
  // Tampilkan semua pembacaan sensor
  displayReadings(temperature, humidity, co2ppm, ozone);
  
  // Cek perubahan signifikan untuk dikirim ke server
  bool hasSignificantChange = checkSignificantChange(temperature, humidity, ozone);
  
  unsigned long currentMillis = millis();
  bool shouldSend = false;
  String reason = "";
  
  if (firstRun) {
    shouldSend = true;
    reason = "First run";
    firstRun = false;
  } 
  else if (hasSignificantChange && (currentMillis - lastSendTime >= MIN_SEND_INTERVAL)) {
    shouldSend = true;
    reason = "Significant change detected";
  }
  else if (currentMillis - lastForceSendTime >= FORCE_SEND_INTERVAL) {
    shouldSend = true;
    reason = "Force send interval reached";
  }
  
  if (shouldSend) {
    Serial.println("=========================================");
    Serial.print("Sending data to server - Reason: ");
    Serial.println(reason);
    Serial.println("=========================================");
    
    sendToServer(temperature, humidity, co2ppm, ozone);
    lastSendTime = currentMillis;
    lastForceSendTime = currentMillis;
    updateLastValues(temperature, humidity, ozone);
  }
  
  delay(500);
}

// ========== CEK PERUBAHAN SIGNIFIKAN ==========
bool checkSignificantChange(float temperature, float humidity, float ozone) {
  bool changed = false;
  
  float tempDiff = abs(temperature - lastTemperature);
  if (tempDiff >= TEMP_THRESHOLD) {
    Serial.printf("Temperature changed: %.1f -> %.1f\n", lastTemperature, temperature);
    changed = true;
  }
  
  float humDiff = abs(humidity - lastHumidity);
  if (humDiff >= HUMIDITY_THRESHOLD) {
    Serial.printf("Humidity changed: %.1f -> %.1f\n", lastHumidity, humidity);
    changed = true;
  }
  
  int pm10Diff = abs(pmsData.pm10 - lastPmsData.pm10);
  if (pm10Diff >= PM10_THRESHOLD) changed = true;
  
  float ozoneDiff = abs(ozone - lastOzone);
  if (ozoneDiff >= OZONE_THRESHOLD) changed = true;
  
  float no2Diff = abs(no2 - lastNo2);
  if (no2Diff >= NO2_THRESHOLD) changed = true;
  
  return changed;
}

void updateLastValues(float temperature, float humidity, float ozone) {
  lastTemperature = temperature;
  lastHumidity = humidity;
  lastOzone = ozone;
  lastNo2 = no2;
  lastPmsData = pmsData;
}

// ========== KIRIM DATA KE SERVER ==========
void sendToServer(float temperature, float humidity, float co2ppm, float ozone) {
  if (WiFi.status() != WL_CONNECTED) {
    connectToWiFi();
    return;
  }
  
  if (isnan(temperature) || isnan(humidity)) {
    Serial.println("Invalid sensor readings, skipping send");
    return;
  }
  
  HTTPClient http;
  
  String url = String(serverUrl) + "?temperature=" + String(temperature) +
               "&humidity=" + String(humidity) +
               "&pm1_0=" + String(pmsData.pm1_0) +
               "&pm2_5=" + String(pmsData.pm2_5) +
               "&pm10=" + String(pmsData.pm10) +
               "&pollutant=" + String(co2ppm) +
               "&ozone=" + String(ozone, 3) +
               "&no2=" + String(no2, 3);
  
  http.begin(url);
  http.setTimeout(5000);
  int httpCode = http.GET();
  
  if (httpCode == HTTP_CODE_OK) {
    Serial.println("Data sent successfully!");
  } else {
    Serial.printf("HTTP error: %d\n", httpCode);
  }
  
  http.end();
}

// ========== KONEKSI WIFI ==========
void connectToWiFi() {
  Serial.print("Connecting to WiFi");
  WiFi.begin(ssid, password);
  
  int attempts = 0;
  while (WiFi.status() != WL_CONNECTED && attempts < 30) {
    delay(500);
    Serial.print(".");
    attempts++;
  }
  
  if (WiFi.status() == WL_CONNECTED) {
    Serial.println("\nWiFi connected!");
    Serial.print("IP address: ");
    Serial.println(WiFi.localIP());
  } else {
    Serial.println("\nWiFi connection failed!");
  }
}

// ========== TAMPILAN DATA DI SERIAL MONITOR ==========
void displayReadings(float temperature, float humidity, float co2ppm, float ozone) {
  Serial.println("=========================================");
  Serial.println("        AIR QUALITY REPORT");
  Serial.println("=========================================");
  
  if (!isnan(temperature) && !isnan(humidity)) {
    Serial.printf("Temp: %.1f°C  Humidity: %.1f%%\n", temperature, humidity);
  }
  
  Serial.printf("PM1.0: %d  PM2.5: %d  PM10: %d μg/m³\n", pmsData.pm1_0, pmsData.pm2_5, pmsData.pm10);
  Serial.printf("CO2: %.1f ppm  Ozone: %.3f ppm\n", co2ppm, ozone);
  Serial.printf("NO2: %.3f ppm (Tegangan: %.3f V)\n", no2, no2_voltage);
  
  Serial.println("=========================================\n");
}