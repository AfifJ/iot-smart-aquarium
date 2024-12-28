<?php
require_once 'config/database.php';
$db = new Database();

$sensorData = $db->getSensorData();
$lampStatus = $db->getLampStatus();
$alarms = $db->getFeedingAlarms();
$feedHistory = $db->getFeedingHistory();
// $servername = "localhost";
// $username = "root";
// $password = "";
// $dbname = "esp32_control";

// // Create connection
// $conn = new mysqli($servername, $username, $password, $dbname);

// // Check connection
// if ($conn->connect_error) {
//     die("Connection failed: " . $conn->connect_error);
// }

// // Handle AJAX request
// if (isset($_POST['led']) && isset($_POST['status'])) {
//     $led = $_POST['led'];
//     $status = $_POST['status'];

//     $sql = "UPDATE led_status SET $led=$status WHERE id=1";
//     $conn->query($sql);
//     exit;
// }

// // Retrieve LED status
// $sql = "SELECT * FROM led_status WHERE id=1";
// $result = $conn->query($sql);
// $row = $result->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Smart Aquarium Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100">
    <div class="container mx-auto px-4 py-8">
        <h1 class="text-3xl font-bold text-center mb-8">Smart Aquarium Dashboard</h1>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Light Sensor Section -->
            <div class="bg-white shadow-md rounded-lg p-6">
                <h2 class="text-xl font-semibold mb-4">Light Sensor Readings</h2>
                <ul class="space-y-2">
                    <li class="flex justify-between">
                        <span>Current Light Level:</span>
                        <span class="font-bold text-blue-600">750 lux</span>
                    </li>
                    <li class="flex justify-between">
                        <span>Status:</span>
                        <span class="text-green-600">Normal</span>
                    </li>
                    <li class="flex justify-between">
                        <span>Last Reading:</span>
                        <span>2023-06-15 14:30:45</span>
                    </li>
                </ul>
            </div>

            <!-- Lamp Control Section -->
            <div class="bg-white shadow-md rounded-lg p-6">
                <h2 class="text-xl font-semibold mb-4">Lamp Control</h2>
                <div class="flex items-center justify-between">
                    <span>Current Status:</span>
                    <span class="font-bold text-green-600">Active</span>
                </div>
                <button class="mt-4 w-full bg-blue-500 text-white py-2 rounded-md hover:bg-blue-600 transition">
                    Toggle Lamp
                </button>
            </div>

            <!-- Feeding Alarm Section -->
            <div class="bg-white shadow-md rounded-lg p-6">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-xl font-semibold">Feeding Alarms</h2>
                    <button id="addAlarmBtn" class="bg-blue-500 text-white px-3 py-1 rounded-md hover:bg-blue-600">
                        + Add Alarm
                    </button>
                </div>
                <div id="alarmContainer" class="space-y-4 max-h-64 overflow-y-auto">

                    <!-- Alarm items akan ditambahkan di sini -->

                    <div class="flex items-center justify-between">
                        <span>Morning Alarm</span>
                        <div class="flex items-center space-x-4">
                            <input type="time" class="border rounded-md px-2 py-1" value="07:00">
                            <label class="switch">
                                <input type="checkbox" checked>
                                <span class="slider round bg-green-500"></span>
                            </label>
                            <button class="text-red-500 hover:text-red-700">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20"
                                    fill="currentColor">
                                    <path fill-rule="evenodd"
                                        d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z"
                                        clip-rule="evenodd" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tambahkan tombol feed manual -->
            <div class="mt-4 flex space-x-4">
                <button class="w-full bg-green-500 text-white py-2 rounded-md hover:bg-green-600 transition">
                    Feed Now (Manual)
                </button>
                <!-- <button class="w-full bg-yellow-500 text-white py-2 rounded-md hover:bg-yellow-600 transition">
                    Custom Feed Amount
                </button> -->
            </div>

            <!-- Feeding History Section -->
            <div class="bg-white shadow-md rounded-lg p-6 col-span-full">
                <h2 class="text-xl font-semibold mb-4">Feeding History</h2>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="bg-gray-200">
                                <th class="py-2 px-4 text-left">Date</th>
                                <th class="py-2 px-4 text-left">Time</th>
                                <th class="py-2 px-4 text-left">Type</th>
                                <th class="py-2 px-4 text-left">Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="border-b">
                                <td class="py-2 px-4">2023-06-15</td>
                                <td class="py-2 px-4">07:00 AM</td>
                                <td class="py-2 px-4">Automatic</td>
                                <td class="py-2 px-4">10g</td>
                            </tr>
                            <tr class="border-b">
                                <td class="py-2 px-4">2023-06-15</td>
                                <td class="py-2 px-4">06:00 PM</td>
                                <td class="py-2 px-4">Automatic</td>
                                <td class="py-2 px-4">10g</td>
                            </tr>
                            <tr class="border-b">
                                <td class="py-2 px-4">2023-06-14</td>
                                <td class="py-2 px-4">07:00 AM</td>
                                <td class="py-2 px-4">Automatic</td>
                                <td class="py-2 px-4">10g</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <style>
        .switch {
            position: relative;
            display: inline-block;
            width: 60px;
            height: 34px;
        }

        .switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }

        .slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #ccc;
            transition: .4s;
            border-radius: 34px;
        }

        .slider:before {
            position: absolute;
            content: "";
            height: 26px;
            width: 26px;
            left: 4px;
            bottom: 4px;
            background-color: white;
            transition: .4s;
            border-radius: 50%;
        }

        input:checked+.slider:before {
            transform: translateX(26px);
        }
    </style>
    <script>
        document.getElementById('addAlarmBtn').addEventListener('click', function () {
            const container = document.getElementById('alarmContainer');
            const newAlarm = document.createElement('div');
            newAlarm.className = 'flex items-center justify-between';
            newAlarm.innerHTML = `<span>New Alarm</span>
                                    <div class="flex items-center space-x-4">
                                        <input 
                                            type="time" 
                                            class="border rounded-md px-2 py-1"
                                            value="12:00"
                                        >
                                        <label class="switch">
                                            <input type="checkbox" checked>
                                            <span class="slider round bg-green-500"></span>
                                        </label>
                                        <button onclick="this.closest('div').parentElement.remove()" class="text-red-500 hover:text-red-700">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                                            </svg>
                                        </button>
                                    </div>
                                `;
            container.appendChild(newAlarm);
        });
    </script>
</body>

</html>˝