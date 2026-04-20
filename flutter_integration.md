# 🚀 Flutter Integration Guide: App Store Backend

This guide explains how to connect your Flutter application to the Laravel backend.

## 📍 1. Connection Setup

### Base URL Configuration
When testing locally, use your machine's IP address (not `localhost`) for physical devices. For Render, use your generated `.onrender.com` URL.

```dart
class ApiConfig {
  static const String localUrl = "http://10.0.2.2:8000/api"; // Android Emulator
  // static const String localUrl = "http://192.168.1.XX:8000/api"; // Physical Device
  static const String renderUrl = "https://your-app-name.onrender.com/api";
  
  static String get baseUrl => renderUrl; // Switch here after deployment
}
```

### Recommended Packages
Add these to your `pubspec.yaml`:
```yaml
dependencies:
  dio: ^5.4.0 # Better for file uploads and interceptors
  flutter_secure_storage: ^9.0.0 # For storing the Bearer Token
```

---

## 🔐 2. Authentication Flow

### Login & Token Storage
1. Send POST to `/register` or `/login`.
2. Save the `token` securely.
3. Attach the token to the `Authorization` header for all subsequent requests.

```dart
// Example Login
final response = await dio.post('${ApiConfig.baseUrl}/login', data: {
  'email': email,
  'password': password,
});

String token = response.data['token'];
await storage.write(key: 'auth_token', value: token);
```

---

## 📱 3. Core API Endpoints

### Fetching Apps
**GET** `/apps`
Returns a list of active apps with their latest version.

### Checking for Updates
**GET** `/apps/check-update?package=com.example.app&version_code=10`
Used for the "In-App Update" feature.

```dart
final response = await dio.get(
  '${ApiConfig.baseUrl}/apps/check-update',
  queryParameters: {
    'package': 'com.myapp.package',
    'version_code': 1,
  },
);

if (response.data['update'] == true) {
  String apkUrl = response.data['apk_url'];
  // Trigger download/install
}
```

### Uploading a Submission (Developer Feature)
This is a two-step process:
1. **POST** `/submissions/upload-apk`: Upload the file to S3 (Multipart).
2. **POST** `/submissions`: Send the metadata and the `temp_path` returned from step 1.

---

## ☁️ 4. Render Deployment Tips

### Environment Variables
When moving to Render, ensure you set these in the Render Dashboard:
- `APP_KEY`: Generated via `php artisan key:generate --show`.
- `DB_CONNECTION`: `pgsql` (Render provides a managed PostgreSQL DB).
- `FILESYSTEM_DISK`: `s3`.
- `AWS_ACCESS_KEY_ID`, `AWS_SECRET_ACCESS_KEY`, etc.

### Database Setup on Render
Since we used SQLite locally for testing, you must run migrations on Render after connecting the PostgreSQL database:
1. Connect to the Render Shell.
2. Run: `php artisan migrate --force`.
3. Run: `php artisan db:seed --class=AdminUserSeeder`.

### HTTPS Requirement
Render provides automatic SSL (HTTPS). Ensure your Flutter app always uses `https://` for production to avoid "Cleartext traffic not permitted" errors on Android.

---

## 🛠 5. Global Headers
Always include these headers in your Dio instance:
```dart
dio.options.headers = {
  'Accept': 'application/json',
  'Content-Type': 'application/json',
};
```
