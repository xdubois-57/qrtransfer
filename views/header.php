<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../controllers/LanguageController.php';
$lang = LanguageController::getInstance();
?>
<!DOCTYPE html>
<html lang="<?php echo $lang->getCurrentLanguage(); ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $lang->translate('app_name'); ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@picocss/pico@2/css/pico.min.css">
    <link rel="stylesheet" href="/css/styles.css">
    
    <script>
    // Expose PHP translations to JavaScript
    window.t = function(key) {
        const translations = {
            'save_favorite': '<?php echo $lang->translate('save_favorite'); ?>',
            'update_favorite': '<?php echo $lang->translate('update_favorite'); ?>',
            // Add other frequently used translations
            'app_name': '<?php echo $lang->translate('app_name'); ?>'
        };
        return translations[key] || key;
    };
    </script>
</head>
<body>
    <nav>
        <div class="container-fluid">
            <div class="nav-header">
                <button class="hamburger" onclick="toggleMenu()">
                    <svg viewBox="0 0 100 80" width="20" height="20">
                        <rect width="100" height="10"></rect>
                        <rect y="30" width="100" height="10"></rect>
                        <rect y="60" width="100" height="10"></rect>
                    </svg>
                </button>
                <strong><?php echo $lang->translate('app_name'); ?></strong>
            </div>

            <div class="nav-links">
                <ul>
                    <li><a href="/"><?php echo $lang->translate('menu_home'); ?></a></li>
                    <li><a href="/why-us"><?php echo $lang->translate('menu_why_us'); ?></a></li>
                    <li><a href="/gdpr"><?php echo $lang->translate('menu_gdpr'); ?></a></li>
                    <li>
                        <select onchange="changeLanguage(this.value)" aria-label="<?php echo $lang->translate('language'); ?>">
                            <?php
                            $config = require __DIR__ . '/../config/languages.php';
                            $languages = $config['available_languages'];
                            asort($languages); // Sort languages by name
                            foreach ($languages as $code => $name) {
                                $selected = $lang->getCurrentLanguage() === $code ? 'selected' : '';
                                echo "<option value=\"$code\" $selected>$name</option>";
                            }
                            ?>
                        </select>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <script src="/js/form-validation.js" type="module"></script>
    <script>
        function toggleMenu() {
            document.querySelector('.nav-links').classList.toggle('active');
        }

        async function changeLanguage(lang) {
            // Create a form and submit it to properly handle the language change
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '/language/' + lang;
            
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'lang';
            input.value = lang;
            
            form.appendChild(input);
            document.body.appendChild(form);
            form.submit();
        }
    </script>
