<?php
session_start();

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: index.php");
    exit;
}

 $mediaDir = 'media/';
 $imageDir = 'images/';

 $mediaFiles = [];
if (is_dir($mediaDir)) {
    $files = scandir($mediaDir);
    foreach ($files as $file) {
        if ($file !== '.' && $file !== '..' && $file !== '.DS_Store') {
            $mediaFiles[] = $file;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Futon</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background-color: #000000;
            color: #f5f5f7;
            min-height: 100vh;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }

        .page-wrap {
            max-width: 1400px;
            margin: 0 auto;
            padding: 40px 60px 100px;
        }

        @media (max-width: 768px) {
            .page-wrap { padding: 20px 24px 80px; }
        }

        .top-bar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding-bottom: 40px;
        }

        .logo-text {
            font-size: 24px;
            font-weight: 700;
            letter-spacing: -0.5px;
            text-transform: lowercase;
        }

        .logout-btn {
            font-family: 'Inter', sans-serif;
            background: rgba(255,255,255,0.08);
            color: #f5f5f7;
            border: none;
            padding: 8px 20px;
            border-radius: 980px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 500;
            transition: background 0.2s ease;
        }

        .logout-btn:hover {
            background: rgba(255,255,255,0.15);
        }

        .welcome-section {
            margin-bottom: 60px;
        }

        .welcome-section h1 {
            font-size: 48px;
            font-weight: 700;
            letter-spacing: -1.5px;
            line-height: 1.1;
            margin-bottom: 12px;
        }

        .status-row {
            display: flex;
            gap: 24px;
            margin-top: 20px;
        }

        .status-chip {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 14px;
            color: #86868b;
            font-weight: 500;
        }

        .status-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: #30d158;
        }

        .section {
            margin-bottom: 64px;
        }

        .section-header {
            margin-bottom: 24px;
        }

        .section-header h2 {
            font-size: 28px;
            font-weight: 700;
            letter-spacing: -0.5px;
        }

        .upload-card {
            background: rgba(28, 28, 30, 0.8);
            backdrop-filter: blur(40px);
            -webkit-backdrop-filter: blur(40px);
            border-radius: 20px;
            padding: 32px;
        }

        .upload-msg {
            background: rgba(48, 209, 88, 0.12);
            border: 1px solid rgba(48, 209, 88, 0.2);
            color: #30d158;
            padding: 14px 20px;
            border-radius: 12px;
            margin-bottom: 24px;
            font-weight: 500;
            font-size: 14px;
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 24px;
        }

        @media (max-width: 600px) {
            .form-row { grid-template-columns: 1fr; }
            .welcome-section h1 { font-size: 32px; }
        }

        .field-group label {
            display: block;
            font-size: 13px;
            font-weight: 600;
            color: #86868b;
            margin-bottom: 10px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .file-input-wrap {
            position: relative;
            border: 2px dashed rgba(255,255,255,0.1);
            border-radius: 16px;
            padding: 36px 20px;
            text-align: center;
            cursor: pointer;
            transition: all 0.25s ease;
            background: rgba(0,0,0,0.4);
        }

        .file-input-wrap:hover {
            border-color: rgba(255,255,255,0.25);
            background: rgba(255,255,255,0.03);
        }

        .file-input-wrap input[type="file"] {
            position: absolute;
            inset: 0;
            opacity: 0;
            cursor: pointer;
            width: 100%;
            height: 100%;
        }

        .file-input-label {
            font-size: 14px;
            color: #86868b;
            pointer-events: none;
        }

        .file-input-label strong {
            color: #f5f5f7;
            display: block;
            margin-bottom: 4px;
            font-size: 15px;
            font-weight: 600;
        }

        .submit-btn {
            font-family: 'Inter', sans-serif;
            width: 100%;
            background: #f5f5f7;
            color: #000000;
            border: none;
            padding: 14px;
            border-radius: 980px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .submit-btn:hover {
            background: #ffffff;
            transform: scale(1.01);
        }

        .submit-btn:active {
            transform: scale(0.98);
        }

        .media-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
            gap: 24px;
        }

        .media-item {
            border-radius: 20px;
            overflow: hidden;
            background: #1c1c1e;
            transition: transform 0.35s cubic-bezier(0.25, 0.46, 0.45, 0.94), box-shadow 0.35s ease;
            cursor: pointer;
        }

        .media-item:hover {
            transform: scale(1.03);
            box-shadow: 0 20px 50px rgba(0,0,0,0.8);
            z-index: 10;
        }

        .thumb-area {
            width: 100%;
            height: 280px;
            overflow: hidden;
            position: relative;
            background: #000;
        }

        .thumb-area img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.5s ease;
        }

        .media-item:hover .thumb-area img {
            transform: scale(1.05);
        }

        .no-thumb {
            width: 100%;
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #2c2c2e;
            font-size: 48px;
            font-weight: 700;
        }

        .media-info {
            padding: 20px;
        }

        .media-filename {
            font-size: 15px;
            color: #e8e8ed;
            margin-bottom: 16px;
            word-break: break-all;
            line-height: 1.4;
            font-weight: 600;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .play-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            padding: 8px 18px;
            font-family: 'Inter', sans-serif;
            font-size: 13px;
            font-weight: 600;
            color: #000000;
            background: rgba(255,255,255,0.9);
            border: none;
            border-radius: 980px;
            text-decoration: none;
            transition: all 0.2s ease;
        }

        .play-btn:hover {
            background: #ffffff;
            text-decoration: none;
            transform: scale(1.05);
        }

        .play-btn svg {
            width: 12px;
            height: 12px;
        }

        .empty-state {
            text-align: center;
            padding: 80px 20px;
            color: #48484a;
        }

        .empty-state p {
            font-size: 17px;
            font-weight: 500;
        }
    </style>
</head>
<body>
    <div class="page-wrap">

        <div class="top-bar">
            <div class="logo-text">futon</div>
            <form method="POST" action="logout.php">
                <button type="submit" class="logout-btn">Logout</button>
            </form>
        </div>

        <div class="welcome-section">
            <h1>Home</h1>
            <div class="status-row">
                <div class="status-chip">
                    <div class="status-dot"></div>
                    System Online
                </div>
                <div class="status-chip">Upload Limit: 1GB</div>
            </div>
        </div>

        <div class="section">
            <div class="section-header">
                <h2>Upload</h2>
            </div>
            <div class="upload-card">
                <?php
                if (isset($_SESSION['upload_message'])) {
                    echo "<div class='upload-msg'>" . $_SESSION['upload_message'] . "</div>";
                    unset($_SESSION['upload_message']);
                }
                ?>
                <form action="upload.php" method="post" enctype="multipart/form-data">
                    <div class="form-row">
                        <div class="field-group">
                            <label>Media File</label>
                            <div class="file-input-wrap">
                                <input type="file" name="fileToUpload" id="fileToUpload" required>
                                <div class="file-input-label">
                                    <strong>Drop file or browse</strong>
                                    MP4, MP3, WebM, OGG, WAV
                                </div>
                            </div>
                        </div>
                        <div class="field-group">
                            <label>Cover Art (Optional)</label>
                            <div class="file-input-wrap">
                                <input type="file" name="imageToUpload" id="imageToUpload" accept="image/png, image/jpeg">
                                <div class="file-input-label">
                                    <strong>Drop image or browse</strong>
                                    JPG, PNG
                                </div>
                            </div>
                        </div>
                    </div>
                    <button type="submit" name="submit" class="submit-btn">Upload to Server</button>
                </form>
            </div>
        </div>

        <div class="section">
            <div class="section-header">
                <h2>Library</h2>
            </div>
            <div class="media-grid">
                <?php if (empty($mediaFiles)): ?>
                    <div class="empty-state">
                        <p>No media uploaded yet.</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($mediaFiles as $file):
                        $fileInfo = pathinfo($file);
                        $filenameNoExt = $fileInfo['filename'];
                        $extension = isset($fileInfo['extension']) ? strtolower($fileInfo['extension']) : '';
                        $isMedia = in_array($extension, ['mp4', 'mp3', 'webm', 'ogg', 'wav', 'mkv', 'avi']);
                        $hasCustomThumbnail = false;
                        $thumbnailPath = '';

                        if ($isMedia) {
                            $possibleImages = [
                                $imageDir . $filenameNoExt . '.jpg',
                                $imageDir . $filenameNoExt . '.jpeg',
                                $imageDir . $filenameNoExt . '.png'
                            ];
                            foreach ($possibleImages as $img) {
                                if (file_exists($img)) {
                                    $thumbnailPath = $img;
                                    $hasCustomThumbnail = true;
                                    break;
                                }
                            }
                        }
                    ?>
                        <div class="media-item">
                            <div class="thumb-area">
                                <?php if ($hasCustomThumbnail): ?>
                                    <img src="<?php echo htmlspecialchars($thumbnailPath); ?>?t=<?php echo time(); ?>" alt="Cover Art">
                                <?php else: ?>
                                    <div class="no-thumb">&#9835;</div>
                                <?php endif; ?>
                            </div>
                            <div class="media-info">
                                <div class="media-filename"><?php echo htmlspecialchars($file); ?></div>
                                <a href="<?php echo $mediaDir . htmlspecialchars($file); ?>" target="_blank" class="play-btn">
                                    <svg viewBox="0 0 24 24" fill="currentColor"><polygon points="5 3 19 12 5 21 5 3"></polygon></svg>
                                    Play
                                </a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>

    </div>
</body>
</html>
