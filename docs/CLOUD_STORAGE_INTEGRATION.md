# ☁️ **CLOUD STORAGE INTEGRATION - COMPLETE GUIDE**

## ✅ **SUPPORTED CLOUD STORAGE PROVIDERS**

BitMews now supports **9 major cloud storage providers**:

### **1. AWS S3** ⭐ (Most Popular)
- Amazon's industry-standard object storage
- Global availability
- Pay-as-you-go pricing

### **2. DigitalOcean Spaces** 💧
- S3-compatible storage
- Simple pricing ($5/month for 250GB)
- Built-in CDN

### **3. Wasabi** 🌶️
- 80% cheaper than AWS S3
- No egress fees
- Fast performance

### **4. Google Cloud Storage** 🔵
- Google's cloud storage
- Multi-regional redundancy
- Integrated with GCP

### **5. Backblaze B2** 📦
- Very affordable ($5/TB/month)
- No minimum fees
- S3-compatible API

### **6. Cloudflare R2** ☁️
- Zero egress fees
- Global edge network
- S3-compatible

### **7. MinIO** 🏠
- Self-hosted S3-compatible
- Open source
- Full control

### **8. Linode Object Storage** 🔷
- Simple pricing
- S3-compatible
- Global availability

### **9. Vultr Object Storage** ⚡
- Fast performance
- S3-compatible
- Multiple regions

---

## 🚀 **QUICK START**

### **Step 1: Choose Your Provider**

Pick one based on your needs:
- **Best Overall:** AWS S3
- **Best Value:** Wasabi or Backblaze B2
- **Easiest:** DigitalOcean Spaces
- **Zero Egress:** Cloudflare R2
- **Self-Hosted:** MinIO

### **Step 2: Get Credentials**

Each provider gives you:
- Access Key ID
- Secret Access Key
- Bucket Name
- Region/Endpoint

### **Step 3: Configure in .env**

Add to your `.env` file:

---

## 📋 **CONFIGURATION EXAMPLES**

### **AWS S3:**
```env
FILESYSTEM_DISK=s3

AWS_ACCESS_KEY_ID=your_access_key
AWS_SECRET_ACCESS_KEY=your_secret_key
AWS_DEFAULT_REGION=us-east-1
AWS_BUCKET=your-bucket-name
AWS_URL=https://your-bucket.s3.amazonaws.com
```

### **DigitalOcean Spaces:**
```env
FILESYSTEM_DISK=digitalocean

DO_SPACES_KEY=your_access_key
DO_SPACES_SECRET=your_secret_key
DO_SPACES_REGION=nyc3
DO_SPACES_BUCKET=your-space-name
DO_SPACES_ENDPOINT=https://nyc3.digitaloceanspaces.com
DO_SPACES_URL=https://your-space.nyc3.digitaloceanspaces.com
```

### **Wasabi:**
```env
FILESYSTEM_DISK=wasabi

WASABI_ACCESS_KEY_ID=your_access_key
WASABI_SECRET_ACCESS_KEY=your_secret_key
WASABI_DEFAULT_REGION=us-east-1
WASABI_BUCKET=your-bucket-name
WASABI_ENDPOINT=https://s3.wasabisys.com
```

### **Google Cloud Storage:**
```env
FILESYSTEM_DISK=gcs

GOOGLE_CLOUD_PROJECT_ID=your-project-id
GOOGLE_CLOUD_KEY_FILE=/path/to/service-account.json
GOOGLE_CLOUD_STORAGE_BUCKET=your-bucket-name
```

### **Backblaze B2:**
```env
FILESYSTEM_DISK=backblaze

BACKBLAZE_KEY_ID=your_key_id
BACKBLAZE_APPLICATION_KEY=your_app_key
BACKBLAZE_REGION=us-west-002
BACKBLAZE_BUCKET=your-bucket-name
BACKBLAZE_ENDPOINT=https://s3.us-west-002.backblazeb2.com
```

### **Cloudflare R2:**
```env
FILESYSTEM_DISK=r2

R2_ACCESS_KEY_ID=your_access_key
R2_SECRET_ACCESS_KEY=your_secret_key
R2_BUCKET=your-bucket-name
R2_ENDPOINT=https://your-account-id.r2.cloudflarestorage.com
```

### **MinIO (Self-Hosted):**
```env
FILESYSTEM_DISK=minio

MINIO_ACCESS_KEY_ID=minioadmin
MINIO_SECRET_ACCESS_KEY=minioadmin
MINIO_BUCKET=your-bucket
MINIO_ENDPOINT=http://localhost:9000
```

---

## 🎯 **API ENDPOINTS**

### **Upload Single File**
```http
POST /api/v1/storage/upload
Authorization: Bearer {token}
Content-Type: multipart/form-data

file: (binary)
path: "avatars" (optional)
disk: "s3" (optional, uses default if not specified)
```

**Response:**
```json
{
  "success": true,
  "data": {
    "path": "avatars/image_1234567890_abc123.jpg",
    "url": "https://your-bucket.s3.amazonaws.com/avatars/image_1234567890_abc123.jpg",
    "filename": "image_1234567890_abc123.jpg",
    "size": 245678,
    "mime_type": "image/jpeg",
    "disk": "s3"
  }
}
```

### **Upload Multiple Files**
```http
POST /api/v1/storage/upload-multiple
Authorization: Bearer {token}
Content-Type: multipart/form-data

files[]: (binary)
files[]: (binary)
path: "documents" (optional)
disk: "s3" (optional)
```

### **Upload from URL**
```http
POST /api/v1/storage/upload-url
Authorization: Bearer {token}
Content-Type: application/json

{
  "url": "https://example.com/image.jpg",
  "path": "images",
  "disk": "s3"
}
```

### **Upload Base64 Image**
```http
POST /api/v1/storage/upload-base64
Authorization: Bearer {token}
Content-Type: application/json

{
  "base64": "data:image/png;base64,iVBORw0KGgoAAAANS...",
  "path": "avatars",
  "disk": "s3"
}
```

### **Delete File**
```http
DELETE /api/v1/storage/delete
Authorization: Bearer {token}
Content-Type: application/json

{
  "path": "avatars/image_1234567890_abc123.jpg",
  "disk": "s3"
}
```

### **Get File URL**
```http
POST /api/v1/storage/url
Authorization: Bearer {token}
Content-Type: application/json

{
  "path": "avatars/image_1234567890_abc123.jpg",
  "disk": "s3",
  "temporary": false,
  "minutes": 60
}
```

### **List Files**
```http
GET /api/v1/storage/files?directory=avatars&disk=s3&recursive=false
Authorization: Bearer {token}
```

### **Get Storage Stats**
```http
GET /api/v1/storage/stats?disk=s3
Authorization: Bearer {token}
```

**Response:**
```json
{
  "success": true,
  "data": {
    "disk": "s3",
    "total_files": 1234,
    "total_size": 5678901234,
    "total_size_mb": 5414.23,
    "total_size_gb": 5.29
  }
}
```

### **Test Connection**
```http
GET /api/v1/storage/test?disk=s3
Authorization: Bearer {token}
```

---

## 💻 **USAGE IN CODE**

### **Upload File:**
```php
use App\Services\CloudStorageService;

$storage = new CloudStorageService();

// Upload file
$result = $storage->upload($request->file('avatar'), 'avatars', 's3');

if ($result['success']) {
    $url = $result['url'];
    // Save URL to database
}
```

### **Delete File:**
```php
$storage->delete('avatars/old-image.jpg', 's3');
```

### **Get URL:**
```php
$url = $storage->url('avatars/image.jpg', 's3');
```

### **Temporary URL (Private Files):**
```php
$url = $storage->temporaryUrl('private/document.pdf', 60, 's3');
// URL expires in 60 minutes
```

---

## 🎨 **USE CASES**

### **1. User Avatars**
```php
// Upload avatar to cloud
$result = $storage->upload($request->file('avatar'), 'avatars');
$user->avatar = $result['url'];
$user->save();
```

### **2. Token Logos**
```php
// Upload token logo
$result = $storage->upload($request->file('logo'), 'tokens');
$token->logo_url = $result['url'];
$token->save();
```

### **3. Post Images**
```php
// Upload post images
$images = $storage->uploadMultiple($request->file('images'), 'posts');
foreach ($images as $image) {
    $post->images()->create(['url' => $image['url']]);
}
```

### **4. Documents**
```php
// Upload private document
$result = $storage->upload($request->file('document'), 'documents/private');
// Get temporary URL for download
$downloadUrl = $storage->temporaryUrl($result['path'], 30);
```

---

## 💰 **PRICING COMPARISON**

### **Storage Costs (per GB/month):**
```
AWS S3:          $0.023
DigitalOcean:    $0.020
Wasabi:          $0.0059
Google Cloud:    $0.020
Backblaze B2:    $0.005
Cloudflare R2:   $0.015
Linode:          $0.020
Vultr:           $0.010
MinIO:           Free (self-hosted)
```

### **Egress Costs (per GB):**
```
AWS S3:          $0.09
DigitalOcean:    $0.01
Wasabi:          FREE
Google Cloud:    $0.12
Backblaze B2:    FREE (first 3x storage)
Cloudflare R2:   FREE
Linode:          $0.01
Vultr:           $0.01
MinIO:           FREE (self-hosted)
```

---

## 🔧 **ADMIN PANEL INTEGRATION**

### **Configure in Admin:**
1. Login to `/admin`
2. Go to **Settings** → **Cloud Storage**
3. Select provider
4. Enter credentials
5. Test connection
6. Save

### **Features:**
- ✅ Test connection
- ✅ View storage stats
- ✅ Browse files
- ✅ Upload files
- ✅ Delete files
- ✅ Switch providers

---

## 🎯 **BEST PRACTICES**

### **1. Use CDN**
Enable CDN for faster delivery:
- AWS S3 + CloudFront
- DigitalOcean Spaces (built-in CDN)
- Cloudflare R2 (built-in CDN)

### **2. Organize Files**
Use clear folder structure:
```
/avatars/
/tokens/
/posts/
/documents/
/backups/
```

### **3. Set Permissions**
- Public: Avatars, logos, post images
- Private: Documents, backups

### **4. Backup Strategy**
- Use multiple providers
- Regular backups
- Version control

### **5. Cost Optimization**
- Choose right provider
- Delete unused files
- Use lifecycle policies
- Monitor usage

---

## 🚀 **MIGRATION GUIDE**

### **From Local to Cloud:**
```php
// Get all local files
$files = Storage::disk('local')->allFiles();

// Upload to cloud
foreach ($files as $file) {
    $contents = Storage::disk('local')->get($file);
    Storage::disk('s3')->put($file, $contents);
}
```

### **Between Cloud Providers:**
```php
// Copy from S3 to DigitalOcean
$files = Storage::disk('s3')->allFiles();

foreach ($files as $file) {
    $contents = Storage::disk('s3')->get($file);
    Storage::disk('digitalocean')->put($file, $contents);
}
```

---

## 🎉 **SUMMARY**

**Cloud Storage Integration Includes:**
- ✅ 9 major providers supported
- ✅ Complete API endpoints
- ✅ Upload/download/delete
- ✅ Multiple file upload
- ✅ URL upload
- ✅ Base64 upload
- ✅ Temporary URLs
- ✅ Storage statistics
- ✅ Connection testing
- ✅ Admin panel integration
- ✅ Easy configuration
- ✅ Production-ready

**Choose your provider, configure, and start uploading!** ☁️
