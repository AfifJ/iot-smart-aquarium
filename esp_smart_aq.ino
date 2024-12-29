#include <Wire.h>
#include <ESP8266WiFi.h>
#include <ESP8266HTTPClient.h>
#include <iostream>
#include <ArduinoJson.h>

#include <I2C_RTC.h>
// #include <BH1750FVI.h>
#include <ArtronShop_BH1750.h>
#include "ESP8266_ISR_Servo.h"

// Create the Lightsensor instance
// BH1750FVI LightSensor(BH1750FVI::k_DevModeContLowRes);
// BH1750FVI myLux(0x23);

#define RELAY_PIN 0
#define SERVO_PIN 2

const char *ssid = "cherry";
const char *password = "duaduadua";

WiFiClient wifiClient;
const String serverName = "192.168.74.27:8000";

ArtronShop_BH1750 bh1750(0x23, &Wire);  // Non Jump ADDR: 0x23, Jump ADDR: 0x5C
int servo = -1;
bool enableServo = true;

const int morningAlarmHour = 6;
const int morningAlarmMinute = 30;

const int eveningAlarmHour = 23;
const int eveningAlarmMinute = 29;

static DS3231 RTC;

int pos = 0;

bool morningAlarmTriggered = false;
bool eveningAlarmTriggered = false;

void relayOn();
void relayOff();

void servoOpen() {
  for (pos = 0; pos <= 30; pos += 30) {
    ISR_Servo.setPosition(servo, pos);
  }
};

void servoClose() {
  for (pos = 30; pos >= 0; pos -= 30) {
    ISR_Servo.setPosition(servo, pos);
  }
};


void runServo() {
  servoOpen();
  servoClose();
}

void setup() {
  pinMode(RELAY_PIN, OUTPUT);
  Serial.begin(9600);
  Wire.begin();

  // while (!Serial) {
  //   ;  // wait for serial port to connect. Needed for native USB
  // }

  RTC.begin();
  RTC.setHourMode(CLOCK_H24);

  while (!bh1750.begin()) {
    Serial.println("BH1750 not found !");
    delay(1000);
  }

  servo = ISR_Servo.setupServo(SERVO_PIN);
  if (servo != -1)
    Serial.println(F("Setup Servo1 OK"));
  else
    Serial.println(F("Setup Servo1 failed"));

  Serial.print("Is Clock Running: ");
  if (RTC.isRunning()) {
    // updating the time
    RTC.setDateTime(__TIMESTAMP__);


    Serial.println("No");
    Serial.println("Setting Time");

    // RTC.setHourMode(CLOCK_H12);  //Comment if RTC PCF8563
    RTC.setHourMode(CLOCK_H24);

    Serial.println("New Time Set");
    Serial.print(__TIMESTAMP__);
    delay(1000);
    RTC.setDateTime(__TIMESTAMP__);
    // RTC.setDateTime(__DATE__, __TIME__);

    RTC.startClock();  // Start the Clock;
  }

  // Print the current date and time after setting it
  get_current_time();
}

void loop() {
  Serial.println();
  Serial.println();



  Serial.print("Light: ");
  float lux = bh1750.light();
  if (lux < 0) {
    Serial.println("Error reading light sensor");
  } else {
    Serial.print(lux);
    Serial.print(" lx");
    Serial.println();
    Serial.print("Room: ");
    String status;
    if (lux > 20) {
      relayOff();
      Serial.println("Light!");
      status = "Light";
    } else {
      relayOn();
      Serial.println("Dark  ");
      status = "Dark";
    }

    upload_light_sensor(lux, status);
  }


  runServo();
  int currentHour = RTC.getHours();
  int currentMinute = RTC.getMinutes();

  if (currentHour == morningAlarmHour && currentMinute == morningAlarmMinute && !morningAlarmTriggered) {
    runServo();
    morningAlarmTriggered = true;
  } else if (currentHour != morningAlarmHour || currentMinute != morningAlarmMinute) {
    morningAlarmTriggered = false;
  }

  read_led_status();

  delay(1000);
}

void get_current_time() {
  Serial.println("Current Date & Time:");
  Serial.print(RTC.getDay());
  Serial.print("-");
  Serial.print(RTC.getMonth());
  Serial.print("-");
  Serial.print(RTC.getYear());
  Serial.print(" ");

  Serial.print(RTC.getHours());
  Serial.print(":");
  Serial.print(RTC.getMinutes());
  Serial.print(":");
  Serial.print(RTC.getSeconds());
  Serial.println();
  Serial.print("__DATE__");
  Serial.print(__DATE__);
  Serial.print(" ");
  Serial.println(__TIME__);
}

void relayOn() {
  digitalWrite(RELAY_PIN, LOW);
}

void relayOff() {
  digitalWrite(RELAY_PIN, HIGH);
}

void upload_light_sensor(int lightSource, String status) {
  if (isnan(lightSource)) {
    Serial.println("Gagal membaca sensor ketika mengupload");
    return;
  }
  Serial.println("Berhasil membaca sensor ketika mengupload");

  String light = String(lightSource, 2);

  String lightData;
  lightData += "light_level=" + light;
  lightData += "&status=" + status;

  String endpoint = serverName + "/api/insert_sensor_data.php";

  Serial.println(WiFi.status());

  // if (WiFi.status() == WL_CONNECTED) {
  HTTPClient http;

  http.begin(wifiClient, endpoint);
  http.addHeader("Content-Type", "application/x-www-form-urlencoded");

  int httpResponseCode = http.POST(lightData);
  Serial.print("HTTP Response code update light: ");
  Serial.println(httpResponseCode);


  http.end();
  Serial.println("Berhasil harusnya");
  // }
}

void read_led_status() {
  // if (WiFi.status() == WL_CONNECTED) {
  HTTPClient http;

  String endpoint = serverName + "/api/get_lamp_status.php";

  http.begin(wifiClient, endpoint);  // Updated to use WiFiClient
  int httpResponseCode = http.GET();

  if (httpResponseCode > 0) {
    String payload = http.getString();
    Serial.println(payload);

    DynamicJsonDocument doc(1024);
    deserializeJson(doc, payload);

    int led = doc["status"];

    Serial.print("Disini adalah led : ");
    Serial.println(led);
    if (led == '1') {
      relayOn();
    } else {
      relayOff();
    }

    // int led1 = doc["led1"];
    // int led2 = doc["led2"];
    // int led3 = doc["led3"];
    // int led4 = doc["led4"];

    // digitalWrite(led1Pin, led1);
    // digitalWrite(led2Pin, led2);
    // digitalWrite(led3Pin, led3);
    // digitalWrite(led4Pin, led4);
  } else {
    Serial.println("Error on HTTP request");
  }

  http.end();
  // }
}