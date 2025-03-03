# Qmed Smartward Solution

A comprehensive bed management system for healthcare facilities with vital sign device integrations.

## Overview

Qmed Smartward is a modern hospital bed management system designed to streamline patient admission, bed allocation, and monitoring of ward occupancy. The system integrates with vital sign measurement devices to provide real-time patient monitoring and data collection.

## Features

- **Real-time Bed Management**: Track bed availability, occupancy, and status across all wards
- **Ward Organization**: Manage wards, bed assignments, and ward-specific resources
- **Patient Admissions**: Streamlined patient admission, transfer, and discharge processes
- **Vital Sign Integration**: Connect with medical devices to collect and monitor patient vital signs
- **Analytics Dashboard**: View bed occupancy rates, patient statistics, and resource utilization
- **Device Management**: Configure and maintain connections to medical measurement devices

## Technology Stack

- **Backend**: PHP 8.1.10 with Laravel framework
- **Frontend**: Tailwind CSS for modern, responsive UI
- **Database**: MySQL/MariaDB
- **Device Integration**: Support for various vital sign monitoring devices via API/protocols

## System Requirements

- PHP 8.1 or higher
- Composer
- Node.js and NPM
- MySQL 5.7+ or MariaDB 10.2+
- Web server (Apache, Nginx)

## Installation

1. Clone the repository:
   ```
   git clone https://github.com/your-organization/qmed-smartward.git
   cd qmed-smartward
   ```

2. Install PHP dependencies:
   ```
   composer install
   ```

3. Install frontend dependencies:
   ```
   npm install
   ```

4. Copy the environment file and generate application key:
   ```
   cp .env.example .env
   php artisan key:generate
   ```

5. Configure database connection in `.env` file

6. Run migrations:
   ```
   php artisan migrate
   ```

7. Compile frontend assets:
   ```
   npm run dev
   ```

8. Start the development server:
   ```
   php artisan serve
   ```

## Device Integration

The system supports integration with various vital sign monitoring devices through:

- TCP/IP connections
- Serial port interfaces
- Bluetooth connections
- REST API endpoints

Refer to the device integration documentation for detailed setup instructions.

## Key Modules

1. **Bed Management**: Core module for tracking and managing beds across wards
2. **Patient Records**: Store and manage patient information
3. **Admissions**: Handle patient admissions, transfers, and discharges
4. **Vital Signs**: Collect and visualize patient vital measurements
5. **Medical Devices**: Configure and connect to various monitoring equipment

## License

This project is licensed under the MIT License - see the LICENSE file for details.

## Support

For support and questions, please contact support@qmedsolutions.com
