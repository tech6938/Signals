# Notification System

## Overview
This notification system allows administrators to send push notifications to users through Firebase Cloud Messaging (FCM). The system supports sending notifications to different user groups and individual users.

## Features

### 1. Notification Types
- **All Users**: Send to all registered users
- **Subscribers Only**: Send to users with type 'subscriber'
- **Non-Subscribers Only**: Send to users with type 'simple_user'
- **Staff Only**: Send to users with staff_type 'staff'
- **Individual Users**: Send to specific selected users

### 2. Notification Management
- Create and send notifications with title and description
- View notification history and status
- Resend failed notifications
- Edit notification content (before sending)
- Delete notifications

### 3. User Search
- Search users by name or email for individual notifications
- Real-time search with autocomplete
- Select multiple users for targeted notifications

## How to Use

### Accessing Notifications
1. Log in as an admin with `manageNotifications` permission
2. Navigate to **Notifications** in the sidebar
3. Choose **Send Notification** or **Notification List**

### Sending a Notification
1. Click **Send Notification**
2. Fill in the notification title and description
3. Select the recipient type:
   - All Users: Broadcast to everyone
   - Subscribers/Non-Subscribers: Target specific user types
   - Staff: Send to staff members only
   - Individual: Search and select specific users
4. Click **Send Notification**

### Managing Notifications
- **View Details**: Click the eye icon to see full notification details
- **Resend**: Click the paper plane icon to resend a notification
- **Delete**: Click the trash icon to remove a notification
- **Edit**: Click the edit icon to modify notification content

## Technical Details

### Database Structure
- `notifications` table stores notification records
- `target_users` JSON field stores individual user IDs
- Tracks sent status, count, and timestamp

### Permissions Required
- `manageNotifications`: Required to access notification features

### Firebase Integration
- Uses existing FcmService for sending notifications
- Supports rich content with emojis, links, and formatting
- Automatic retry and error handling

## Files Created/Modified

### New Files
- `app/Models/Notification.php` - Notification model
- `app/Http/Controllers/NotificationController.php` - Main controller
- `database/migrations/2025_01_20_000000_create_notifications_table.php` - Database migration
- `database/seeders/NotificationPermissionSeeder.php` - Permission seeder
- `resources/views/admin/notifications/` - All notification views

### Modified Files
- `routes/web.php` - Added notification routes
- `resources/views/admin/includes/sidebar.blade.php` - Added notification menu
- `database/seeders/DatabaseSeeder.php` - Added permission seeder

## Setup Instructions

1. Run migrations: `php artisan migrate`
2. Seed permissions: `php artisan db:seed --class=NotificationPermissionSeeder`
3. Assign `manageNotifications` permission to admin roles
4. Ensure Firebase is properly configured

## Usage Examples

### Send to All Users
```
Title: System Maintenance
Description: The system will be under maintenance from 2 AM to 4 AM EST. Thank you for your patience.
Send To: All Users
```

### Send to Subscribers Only
```
Title: Premium Feature Update
Description: New premium features are now available! Check out the latest updates in your dashboard.
Send To: Subscribers Only
```

### Send to Individual Users
```
Title: Welcome Message
Description: Welcome to our platform! We're excited to have you on board.
Send To: Individual Users (select specific users)
```

## Troubleshooting

### Common Issues
1. **Permission Denied**: Ensure user has `manageNotifications` permission
2. **Firebase Errors**: Check Firebase configuration and credentials
3. **No Users Found**: Verify user search criteria and database content
4. **Notifications Not Sent**: Check FCM tokens and Firebase service status

### Support
For technical issues, check:
- Laravel logs: `storage/logs/laravel.log`
- Firebase console for delivery status
- Database for notification records and user data
