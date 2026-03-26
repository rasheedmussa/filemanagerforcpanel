# File Manager - Temporary Upload System

## Overview
This is a **simple, temporary file upload system** with a modern interface. Files are uploaded for temporary use and can be deleted when no longer needed. **No database required** - everything works with the filesystem.

## Features
- ✅ Single and multiple file uploads
- ✅ Folder upload with structure preservation
- ✅ Drag & drop upload interface
- ✅ File/folder management (create, rename, delete)
- ✅ File preview (images, PDFs, text files)
- ✅ Download functionality
- ✅ Search and sort capabilities
- ✅ Responsive design
- ✅ **No database needed**
- ✅ **No authentication required**
- ✅ **Perfect for temporary file uploads**

## Requirements
- PHP 7.4 or higher
- Web server (Apache/Nginx)
- cPanel hosting account

## PHP Configuration (Important for Large Uploads)
For large file uploads, increase PHP limits in your cPanel:

1. Go to **MultiPHP INI Editor** in cPanel
2. Select your domain
3. Update these settings:
   - `upload_max_filesize = 1024M` (1GB)
   - `post_max_size = 1024M` (1GB)
   - `max_execution_time = 300` (5 minutes)
   - `max_input_time = 300` (5 minutes)
   - `memory_limit = 512M`

## Installation Steps

### 1. Upload Files to Server
1. Upload all project files to your cPanel's `public_html` directory
2. Make sure the `uploads/` directory is writable (chmod 755)

### 2. Access the Application
- Visit `https://yourdomain.com/`
- **File manager works immediately - no setup required!**
- Start uploading files right away

## File Size Limits
- Maximum file size: **1GB per file**
- No database storage limits
- Only filesystem space limits apply

## How It Works
- **File Storage**: Files stored directly in `uploads/` folder
- **File Listing**: Uses PHP filesystem functions
- **File Info**: Gets size, type, modified time from filesystem
- **Operations**: Create, rename, delete use PHP filesystem functions
- **Security**: Path traversal protection

## Usage
1. Upload files via drag & drop or file picker
2. Create folders for organization
3. Preview files (images, PDFs, text)
4. Download files when needed
5. Delete files when done

## Perfect For
- Temporary file sharing
- Quick file uploads
- Development testing
- Backup storage
- File processing workflows

## Security Features
- File type validation
- File size limits
- Filename sanitization
- Path traversal prevention
- PHP execution prevention in uploads

## File Structure
```
filemanager/
├── index.php              # Entry point
├── dashboard.php          # Main dashboard
├── upload.php             # Upload handler
├── create_folder.php      # Create folder handler
├── rename.php             # Rename handler
├── delete.php             # Delete handler
├── download.php           # Download handler
├── preview.php            # Preview handler
├── config.php             # Configuration
├── .htaccess              # Security rules
├── uploads/               # Upload directory
│   └── .htaccess          # Upload security
└── assets/
    ├── css/
    │   └── style.css      # Styles
    └── js/
        └── main.js        # JavaScript
```

## Advantages
- 🚀 **Zero configuration** - works out of the box
- ⚡ **Fast performance** - direct filesystem access
- 🛡️ **No database errors** - no connection issues
- 💾 **Simple backup** - backup files folder only
- 🌐 **Works everywhere** - any PHP hosting
- 🧹 **Clean system** - no unnecessary complexity

**This is a pure filesystem-based file manager perfect for temporary uploads!** 🎉

## Requirements
- PHP 7.4 or higher
- Web server (Apache/Nginx)
- cPanel hosting account
- **No MySQL database required for file operations**

## PHP Configuration (Important for Large Uploads)
Before uploading large files, you need to increase PHP limits in your cPanel:

1. Go to **MultiPHP INI Editor** in cPanel
2. Select your domain
3. Update these settings:
   - `upload_max_filesize = 1024M` (1GB)
   - `post_max_size = 1024M` (1GB)
   - `max_execution_time = 300` (5 minutes)
   - `max_input_time = 300` (5 minutes)
   - `memory_limit = 512M`

4. Click **Apply** and wait for changes to take effect

## File Size Limits
- Maximum file size: **1GB per file**
- Maximum total upload size per request: 1GB
- Supported file types: Images, PDFs, Documents, Videos, Audio, Archives

## Installation Steps

### 1. Upload Files to Server
1. Download all the project files
2. Upload them to your cPanel's `public_html` directory or a subdirectory
3. Make sure the `uploads/` directory is writable (chmod 755 or 777)

### 2. Create Database
1. Log into your cPanel
2. Go to "MySQL Databases"
3. Create a new database (e.g., `filemanager`)
4. Create a database user and assign it to the database with full privileges
5. Note down the database name, username, and password

### 3. Import Database Schema
1. Go to "phpMyAdmin" in cPanel
2. Select your database
3. Click "Import" tab
4. Upload the `db_schema.sql` file
5. Click "Go" to import

### 4. Configure Database Connection
1. Open `config.php` in your file manager
2. Update the database constants:
   ```php
   define('DB_HOST', 'localhost');
   define('DB_USER', 'your_db_user');
   define('DB_PASS', 'your_db_password');
   define('DB_NAME', 'your_db_name');
   ```

### 2. Optional: Setup Authentication Database
If you want login system:
1. Create MySQL database
2. Create database user with password
3. Import `db_schema.sql`
4. Update `config.php` with your actual database credentials

**Skip this step completely - the file manager will work perfectly without any database!**

### 3. Set Permissions
1. In cPanel File Manager, right-click the `uploads/` folder
2. Set permissions to 755 (or 777 if needed)
3. Make sure `.htaccess` files are uploaded and working

### 4. Access the Application
- Visit `https://yourdomain.com/`
- **No database? No problem!** - File manager works immediately
- If database configured: login with `admin` / `admin123`
- Start uploading files right away!

## Security Notes
- The `.htaccess` files prevent direct access to uploaded PHP files
- File names are sanitized to prevent path traversal
## How It Works Without Database
- **File Storage**: Files stored directly in `uploads/` directory
- **File Listing**: Uses PHP `DirectoryIterator` to scan filesystem
- **File Info**: Gets size, type, modified time from filesystem
- **Operations**: Create, rename, delete use PHP filesystem functions
- **Security**: Path traversal protection with `realpath()` checks

## Advantages of No Database Approach
- ✅ **Simpler deployment** - no database setup required
- ✅ **Better performance** - direct filesystem access
- ✅ **No database corruption issues**
- ✅ **Easier backup** - just backup files
- ✅ **Works on shared hosting without MySQL**

## Usage Without Database
1. Upload files - they go directly to `uploads/` folder
2. Create folders - creates subdirectories
3. Navigate - filesystem-based directory listing
4. All operations work without any database queries

## Optional Authentication
If you want to add login later:
1. Setup database as described
2. Uncomment database code in PHP files
3. Files will be user-specific

The system is designed to work perfectly without any database for file operations!
filemanager/
├── index.php              # Entry point
├── login.php              # Login page
├── logout.php             # Logout handler
├── dashboard.php          # Main dashboard
├── upload.php             # Upload handler
├── create_folder.php      # Create folder handler
├── rename.php             # Rename handler
├── delete.php             # Delete handler
├── download.php           # Download handler
├── preview.php            # Preview handler
├── config.php             # Configuration
├── .htaccess              # Security rules
├── db_schema.sql          # Database schema
├── uploads/               # Upload directory
│   └── .htaccess          # Upload security
└── assets/
    ├── css/
    │   └── style.css      # Styles
    └── js/
        └── main.js        # JavaScript
```

## Customization
- Modify `assets/css/style.css` for custom styling
- Update `config.php` for additional settings
- Add more file type validations in `upload.php`

## Support
This is a complete, working system. Test thoroughly before production use.

## How It Works Without Database
- **File Storage**: Files stored directly in `uploads/` directory
- **File Listing**: Uses PHP `DirectoryIterator` to scan filesystem
- **File Info**: Gets size, type, modified time from filesystem
- **Operations**: Create, rename, delete use PHP filesystem functions
- **Security**: Path traversal protection with `realpath()` checks

## Advantages of No Database Approach
- ✅ **Simpler deployment** - no database setup required
- ✅ **Better performance** - direct filesystem access
- ✅ **No database corruption issues**
- ✅ **Easier backup** - just backup files
- ✅ **Works on shared hosting without MySQL**

## Usage Without Database
1. Upload files - they go directly to `uploads/` folder
2. Create folders - creates subdirectories
3. Navigate - filesystem-based directory listing
4. All operations work without any database queries

## Optional Authentication
If you want to add login later:
1. Setup database as described
2. Uncomment database code in PHP files
3. Files will be user-specific

The system is designed to work perfectly without any database for file operations!# #   T r o u b l e s h o o t i n g   &   D a t a b a s e   I s s u e s 
 
 # # #   D a t a b a s e   C o n n e c t i o n   F a i l e d ? 
 * * N o   p r o b l e m ! * *   T h e   s y s t e m   a u t o m a t i c a l l y   h a n d l e s   d a t a b a s e   i s s u e s : 
 
 -   '  * * D a t a b a s e   w o r k s * *   �!  A u t h e n t i c a t i o n   e n a b l e d ,   l o g i n   r e q u i r e d     
 -   L'  * * D a t a b a s e   f a i l s * *   �!  F i l e s y s t e m - o n l y   m o d e ,   d i r e c t   a c c e s s   t o   f i l e   m a n a g e r 
 -   =��  * * W r o n g   c r e d e n t i a l s * *   �!  S y s t e m   g r a c e f u l l y   f a l l s   b a c k   t o   n o - a u t h e n t i c a t i o n   m o d e 
 
 # # #   T e s t   d a t a b a s e   s t a t u s : 
 V i s i t   \ 	 e s t _ d b . p h p \   t o   v e r i f y   c u r r e n t   s t a t u s . 
 
 # # #   W h a t   h a p p e n s   w i t h o u t   d a t a b a s e : 
 -   F i l e   m a n a g e r   w o r k s   p e r f e c t l y 
 -   F i l e s   s t o r e d   d i r e c t l y   i n   f i l e s y s t e m     
 -   A l l   o p e r a t i o n s   w o r k   ( u p l o a d ,   d e l e t e ,   r e n a m e ) 
 -   N o   a u t h e n t i c a t i o n   r e q u i r e d 
 -   B e t t e r   p e r f o r m a n c e   w i t h   d i r e c t   f i l e s y s t e m   a c c e s s 
 
 * * T h e   s y s t e m   w o r k s   p e r f e c t l y   w i t h   o r   w i t h o u t   d a t a b a s e ! * * 
 
 