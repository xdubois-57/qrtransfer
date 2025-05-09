<?php
/**
 * QR Transfer
 * Copyright (C) 2025 Xavier Dubois
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <https://www.gnu.org/licenses/>.
 */
?>

<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../controllers/LanguageController.php';
require_once __DIR__ . '/../core/AppRegistry.php';

// Initialize the language controller
$lang = LanguageController::getInstance();

// Determine current app
$uriSegments = explode('/', trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/'));
$possibleApp = $uriSegments[1] ?? null; // after lang code
$validApps = ['paid','involved','drive'];
$cur = $currentApp ?? ($_SESSION['current_app'] ?? (in_array($possibleApp, $validApps) ? $possibleApp : 'landing'));
$_SESSION['current_app'] = $cur;

// Set current app in registry
$registry = AppRegistry::getInstance();
$registry->setCurrent($cur);

// Reload translations to include app-specific translations
// This ensures we get both global and app-specific translations
if (method_exists($lang, 'loadTranslations')) {
    $lang->loadTranslations();
}
?>
<!DOCTYPE html>
<html lang="<?php echo $lang->getCurrentLanguage(); ?>">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($pageTitle) ? $pageTitle : $lang->translate('meta_title'); ?></title>
    <meta name="description" content="<?php echo isset($pageDescription) ? $pageDescription : $lang->translate('meta_description'); ?>">
    <meta name="keywords" content="<?php echo $lang->translate('meta_keywords'); ?>">
    <meta name="robots" content="index, follow">
    <meta property="og:title" content="<?php echo isset($pageTitle) ? $pageTitle : $lang->translate('meta_title'); ?>">
    <meta property="og:description" content="<?php echo isset($pageDescription) ? $pageDescription : $lang->translate('meta_description'); ?>">
    <meta property="og:type" content="website">
    <meta property="og:url" content="https://iwantto.be/<?php echo $lang->getCurrentLanguage(); ?><?php echo $_SERVER['REQUEST_URI']; ?>">
    <meta name="google-site-verification" content="VlG6fhlOB4LhJf2uMGbByhfL2mJ3ilaltvhI7i0ChnA" />
    <link rel="canonical" href="https://iwantto.be<?php echo $_SERVER['REQUEST_URI']; ?>">
    <link rel="icon" type="image/x-icon" href="/favicon.ico">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@picocss/pico@2/css/pico.min.css">
    <link rel="stylesheet" href="/css/styles.css">
    <link rel="stylesheet" href="/css/wordcloud.css">
    <?php
    $config = require __DIR__ . '/../config/languages.php';
    $languages = $config['available_languages'];
    asort($languages); // Sort languages by name
    // Add hreflang alternate links for all supported languages
    foreach ($languages as $code => $name) {
        echo '<link rel="alternate" hreflang="' . $code . '" href="https://iwantto.be/' . $code . '/" />';
    }
    ?>
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="<?php echo isset($pageTitle) ? $pageTitle : $lang->translate('meta_title'); ?>">
    <meta name="twitter:description" content="<?php echo isset($pageDescription) ? $pageDescription : $lang->translate('meta_description'); ?>">
    <!-- If you want to add a twitter:image, add it here -->
    <?php if (in_array($lang->getCurrentLanguage(), ['ar', 'he', 'fa', 'ur'])): ?>
        <script>document.documentElement.setAttribute('dir', 'rtl');</script>
    <?php endif; ?>
    
    <?php
    // Load app-specific JavaScript files
    $appRegistry = AppRegistry::getInstance();
    $currentApp = $appRegistry->getCurrent();
    if ($currentApp !== null && $currentApp !== 'landing') {
        // If we have a current app and it's not the landing page
        if (method_exists($currentApp, 'getJavaScriptFiles')) {
            $jsFiles = $currentApp->getJavaScriptFiles();
            foreach ($jsFiles as $jsFile) {
                // Check if it's a full path (starting with /) or app-specific path
                if (strpos($jsFile, '/') === 0) {
                    echo '<script src="' . $jsFile . '"></script>' . PHP_EOL;
                } else {
                    echo '<script src="/apps/' . $currentApp->getSlug() . '/' . $jsFile . '"></script>' . PHP_EOL;
                }
            }
        }
    }
    ?>
    
    <script>
    // Expose PHP translations to JavaScript
    window.t = function(key) {
        const translations = {
            'save_favorite': <?php echo json_encode($lang->translate('save_favorite')); ?>,
            'update_favorite': <?php echo json_encode($lang->translate('update_favorite')); ?>,
            // Add other frequently used translations
            'app_name': 'iwantto.be Paid',
            'generating': <?php echo json_encode($lang->translate('generating')); ?>,
            'share_text': <?php echo json_encode($lang->translate('share_text')); ?>,
            'cookie_notice': <?php echo json_encode($lang->translate('cookie_notice')); ?>,
            'cookie_accept': <?php echo json_encode($lang->translate('cookie_accept')); ?>,
            // Involved app EventQrBlock translations
            'event_code': <?php echo json_encode($lang->translate('event_code')); ?>,
            'event_password': <?php echo json_encode($lang->translate('event_password')); ?>,
            'share_button': <?php echo json_encode($lang->translate('share_button')); ?>,
            'share_title': <?php echo json_encode($lang->translate('share_title')); ?>,
            'copy_success': <?php echo json_encode($lang->translate('copy_success')); ?>,
            'share_error': <?php echo json_encode($lang->translate('share_error')); ?>,
            'share_link_prompt': <?php echo json_encode($lang->translate('share_link_prompt')); ?>,
            // Password prompt translations
            'invalid_password': <?php echo json_encode($lang->translate('invalid_password')); ?>,

            // Involved app specific (OverlayClientHelper, OverlayObjectHelper, etc)
            'admin_link_text': <?php echo json_encode($lang->translate('admin_link_text')); ?>,
            'scan_qr_to_answer': <?php echo json_encode($lang->translate('scan_qr_to_answer')); ?>,
            'add_your_word': <?php echo json_encode($lang->translate('add_your_word')); ?>,
            'wordcloud_failed_delete': <?php echo json_encode($lang->translate('wordcloud_failed_delete')); ?>,
            'wordcloud_error_delete': <?php echo json_encode($lang->translate('wordcloud_error_delete')); ?>,
            'join_event_description': <?php echo json_encode($lang->translate('join_event_description')); ?>,
            'event_code_placeholder': <?php echo json_encode($lang->translate('event_code_placeholder')); ?>,
            'join_event_button': <?php echo json_encode($lang->translate('join_event_button')); ?>,
            'scan_qr_access': <?php echo json_encode($lang->translate('scan_qr_access')); ?>,
            'create_event_title': <?php echo json_encode($lang->translate('create_event_title')); ?>,
            'create_event_description': <?php echo json_encode($lang->translate('create_event_description')); ?>,
            'delete_failed': <?php echo json_encode($lang->translate('delete_failed')); ?>,
            'an_error_occurred': <?php echo json_encode($lang->translate('an_error_occurred')); ?>,
            'short_description': <?php echo json_encode($lang->translate('short_description')); ?>,
            'qrblock_scan_or_visit': <?php echo json_encode($lang->translate('qrblock_scan_or_visit')); ?>,
        };
        return translations[key] || key;
    };
    window.TRANSLATIONS = window.TRANSLATIONS || {};
    window.TRANSLATIONS['qrblock_scan_or_visit'] = <?php echo json_encode($lang->translate('qrblock_scan_or_visit')); ?>;
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
                <?php
                // Get app display name from registry
                $currentApp = $registry->getCurrent();
                $appDisplayName = $currentApp ? $currentApp->getDisplayName() : 'Good';
                ?>
                <a href="/<?php echo $lang->getCurrentLanguage(); ?>" class="app-name"><em style="font-size: 0.6em;">iwantto.be</em> <span style="font-size: 1.1em; font-weight: bold;"><?php echo $appDisplayName; ?></span></a>
            </div>

            <div class="nav-links">
                <ul>
<?php
$langCode = $lang->getCurrentLanguage();
$currentApp = $registry->getCurrent();

// Always show Home link first (landing page)
echo '<li><a href="/' . $langCode . '">' . $lang->translate('menu_home') . '</a></li>';

// On non-app pages, show links to all apps after Home
if ($cur === 'landing' || $cur === 'support' || $cur === 'gdpr') {
    $apps = $registry->getAppInterfaces();
    usort($apps, function($a, $b) {
        return $a->getOrder() <=> $b->getOrder();
    });
    foreach ($apps as $app) {
        $slug = htmlspecialchars($app->getSlug());
        $name = htmlspecialchars($app->getDisplayName());
        echo '<li><a href="/' . $langCode . '/' . $slug . '">' . $name . '</a></li>';
    }
}

if ($currentApp && $cur !== 'landing' && $cur !== 'support' && $cur !== 'gdpr') {
    // Get menu items from the current app
    $menuItems = $currentApp->getMenuItems();
    foreach ($menuItems as $item) {
        $url = str_replace('{lang}', $langCode, $item['url']);
        $text = $lang->translate($item['text']);
        echo '<li><a href="' . $url . '">' . $text . '</a></li>';
    }
}

// Common pages for all apps
echo '<li><a href="/' . $langCode . '/support">' . $lang->translate('menu_support') . '</a></li>';
if ($cur === 'landing' || $cur === 'support' || $cur === 'gdpr') {
    echo '<li><a href="/' . $langCode . '/gdpr">' . $lang->translate('menu_gdpr') . '</a></li>';
}

// Only show language and theme switchers on landing, support, and gdpr pages
if ($cur === 'landing' || $cur === 'support' || $cur === 'gdpr') {
?>
                    <li class="language-selector">
                        <select onchange="changeLanguage(this.value)" aria-label="<?php echo $lang->translate('language'); ?>">
                            <?php
                            asort($languages); // Sort languages by name
                            foreach ($languages as $code => $name) {
                                $selected = $lang->getCurrentLanguage() === $code ? 'selected' : '';
                                echo "<option value=\"$code\" $selected>$name</option>";
                            }
                            ?>
                        </select>
                    </li>
                    <li class="theme-selector">
                        <select id="theme-selector" aria-label="<?php echo $lang->translate('theme'); ?>">
                            <option value="light"><?php echo $lang->translate('theme_light'); ?></option>
                            <option value="dark"><?php echo $lang->translate('theme_dark'); ?></option>
                            <option value="auto"><?php echo $lang->translate('theme_auto'); ?></option>
                        </select>
                    </li>
<?php
}
?>
                </ul>
            </div>
        </div>
    </nav>

    <script src="/js/form-validation.js" type="module"></script>
    <script>
        function toggleMenu() {
            document.querySelector('.nav-links').classList.toggle('active');
        }

        // Close menu when clicking outside
        document.addEventListener('click', function(event) {
            const navLinks = document.querySelector('.nav-links');
            const hamburger = document.querySelector('.hamburger');
            
            // Only process if we're on mobile (menu toggle is visible) and the menu is open
            if (window.getComputedStyle(hamburger).display !== 'none' && navLinks.classList.contains('active')) {
                // Check if the click was outside the menu and not on the hamburger button
                if (!navLinks.contains(event.target) && !hamburger.contains(event.target)) {
                    navLinks.classList.remove('active');
                }
            }
        });

        function changeLanguage(lang) {
            // Change URL to the selected language, preserving the current path after the language code
            const pathParts = window.location.pathname.split('/').filter(Boolean);
            if (pathParts.length === 0) {
                window.location.href = '/' + lang;
                return;
            }
            // Replace first segment if it's a supported language, otherwise add it
            const supported = <?php echo json_encode(array_keys($languages)); ?>;
            if (supported.includes(pathParts[0])) {
                pathParts[0] = lang;
            } else {
                pathParts.unshift(lang);
            }
            window.location.href = '/' + pathParts.join('/');
        }
    </script>
