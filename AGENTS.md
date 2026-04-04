# AGENTS.md - InstantCMS 2 Development Guide

## Project Overview

**InstantCMS 2** is a PHP 7.2+ open-source CMS written in Russian. It features:
- Modular architecture with controllers (addons)
- Content types system with fields
- Event/hook system for extensibility
- Custom template engine
- Widget system for page composition
- User management and permissions
- Multilingual support

**Repository**: `/Users/maxisoft/Sites/icms2`
**PHP Version**: 7.2+

---

## Directory Structure

```
icms2/
├── system/
│   ├── core/           # Core CMS classes
│   │   ├── autoloader.php       # Custom autoloader
│   │   ├── core.php              # Main singleton (cmsCore)
│   │   ├── controller.php        # Base controller class
│   │   ├── backend.php           # Backend controller base
│   │   ├── frontend.php          # Frontend controller base
│   │   ├── database.php          # Database wrapper (cmsDatabase)
│   │   ├── model.php             # Base model class (cmsModel)
│   │   ├── config.php            # Configuration singleton
│   │   ├── request.php           # HTTP request wrapper
│   │   ├── response.php          # HTTP response
│   │   ├── user.php              # User object
│   │   ├── template.php          # Template engine
│   │   ├── form.php              # Form base class
│   │   ├── formfield.php         # Base field class
│   │   ├── grid.php              # Data grid builder
│   │   ├── eventsmanager.php     # Event/hook system
│   │   ├── permissions.php       # Permission system
│   │   ├── uploader.php          # File upload handling
│   │   ├── images.php            # Image processing
│   │   ├── mailer.php            # Email sending
│   │   ├── cache.php             # Caching abstraction
│   │   ├── cachefiles.php        # File-based cache
│   │   ├── paginator.php         # Pagination
│   │   └── ...
│   ├── controllers/      # Addons/controllers (38 controllers)
│   │   ├── content/     # Content types system
│   │   ├── users/       # User profiles
│   │   ├── auth/        # Authentication
│   │   ├── admin/       # Admin panel
│   │   ├── comments/    # Comments system
│   │   ├── messages/    # Private messages
│   │   ├── photos/      # Photo galleries
│   │   ├── groups/      # User groups
│   │   ├── tags/        # Tags system
│   │   ├── rating/      # Rating system
│   │   ├── search/      # Search functionality
│   │   ├── sitemap/     # Sitemap generation
│   │   └── ...
│   ├── fields/          # Field types (32 field types)
│   │   ├── string.php   # Text input field
│   │   ├── text.php     # Textarea field
│   │   ├── html.php     # HTML editor field
│   │   ├── image.php    # Single image field
│   │   ├── images.php   # Multiple images field
│   │   ├── file.php     # File upload field
│   │   ├── list.php     # Dropdown list field
│   │   ├── checkbox.php # Checkbox field
│   │   ├── date.php     # Date picker field
│   │   ├── number.php   # Number input field
│   │   ├── url.php      # URL field
│   │   ├── user.php     # User selector field
│   │   ├── category.php # Category selector field
│   │   └── ...
│   ├── traits/          # Reusable traits
│   │   ├── controllers/
│   │   │   ├── actions/
│   │   │   │   ├── listgrid.php    # List/grid actions trait
│   │   │   │   ├── formItem.php    # Item form actions
│   │   │   │   └── formFieldItem.php
│   │   │   └── models/
│   │   │       ├── fieldable.php   # Content fields trait
│   │   │       └── transactable.php
│   │   ├── services/
│   │   │   └── fieldsParseable.php
│   │   ├── corePropertyLoadable.php
│   │   └── eventDispatcher.php
│   ├── libs/            # Third-party libraries
│   │   ├── phpmailer/   # Email library
│   │   ├── scssphp/     # SCSS compiler
│   │   ├── jevix.class.php    # HTML/XHTML filter
│   │   ├── spyc.class.php     # YAML parser
│   │   ├── mobile_detect.class.php
│   │   ├── google_authenticator.class.php
│   │   ├── strings.helper.php
│   │   ├── html.helper.php
│   │   ├── files.helper.php
│   │   └── template.helper.php
│   ├── languages/       # Language files
│   │   ├── ru/          # Russian
│   │   │   ├── language.php    # Core language strings
│   │   │   ├── functions.php
│   │   │   ├── controllers/
│   │   │   ├── fields/
│   │   │   ├── letters/        # Email templates
│   │   │   └── widgets/
│   │   └── en/          # English
│   └── config/          # Configuration files
│       ├── autoload.php       # Autoloader config
│       ├── config.php         # Main config
│       └── timezones.php
├── templates/          # Site templates
│   ├── default/
│   ├── modern/
│   └── admincoreui/
├── install/             # Installation files
│   ├── index.php
│   ├── manifest.php
│   ├── manifests/       # Addon manifests
│   ├── steps/           # Installation steps
│   └── languages/
├── upload/              # User-uploaded files
├── cache/               # Cache files
├── bootstrap.php         # Environment initialization
├── index.php             # HTTP entry point
└── cron.php              # CLI entry point
```

---

## Entry Points

### HTTP Request Flow
```
index.php → bootstrap.php → cmsCore::runHttp() → cmsController::runController()
```

1. **index.php** - Main entry point, defines `VALID_RUN` and `SESSION_START` constants
2. **bootstrap.php** - Initializes environment:
   - Defines `PATH` constant
   - Loads Composer autoloader (if exists)
   - Loads CMS autoloader
   - Initializes config (`cmsConfig::getInstance()`)
   - Connects to database
   - Starts cache
   - Fires `core_start` event
3. **cmsCore::runHttp()** - Handles HTTP request:
   - Detects browser language
   - Routes URI to controller/action
   - Initializes template
   - Loads matched widget pages
   - Checks access permissions
   - Runs controller action

### Cron Request Flow
```
cron.php → bootstrap.php → cmsCore::runCli() → cmsController::runController()
```

---

## Autoloading System

The CMS uses a **custom autoloader** (`system/core/autoloader.php`). Class naming conventions map to file paths:

| Class Prefix | File Path | Example |
|--------------|-----------|---------|
| `cms` | `system/core/{name}.php` | `cmsDatabase` → `system/core/database.php` |
| `field` | `system/fields/{name}.php` | `fieldString` → `system/fields/string.php` |
| `model` | `system/controllers/{ctrl}/model.php` | `modelContent` → `system/controllers/content/model.php` |
| `modelBackend` | `system/controllers/{ctrl}/backend/model.php` | `modelBackendContent` → `system/controllers/content/backend/model.php` |
| `icms\` | `system/{path}.php` | `icms\traits\corePropertyLoadable` → `system/traits/corePropertyLoadable.php` |

Register additional namespaces:
```php
cmsAutoloader::register('Namespace\Subnamespace', 'path/to/classes');
cmsAutoloader::registerList([['Namespace', 'path/to/classes']]);
```

---

## Core Classes Reference

### cmsCore
Main singleton. Access via `cmsCore::getInstance()`.

**Key Properties:**
- `$uri` - Current URI without root
- `$uri_controller` - Detected controller name
- `$uri_action` - Detected action name
- `$uri_params` - Action parameters from URI
- `$controller` - Current controller instance
- `$request` - `cmsRequest` object
- `$response` - `cmsResponse` object
- `$db` - `cmsDatabase` object

**Key Methods:**
- `getInstance()` - Get singleton instance
- `route($request_uri)` - Parse URI and determine controller/action
- `runHttp($request_uri)` - Handle HTTP request
- `runController()` - Execute current controller action
- `connectDB()` - Connect to database
- `loadLib($name)` - Load helper library
- `loadLanguage($name)` - Load language file
- `error404()` - Show 404 error page
- `error($message)` - Show error page

### cmsController
Base controller class. All controllers extend this.

**Key Properties:**
- `$name` - Controller name (auto-detected)
- `$model` - Associated model instance
- `$request` - Current request object
- `$options` - Controller options
- `$root_url` - Controller root URL
- `$root_path` - Controller filesystem path

**Key Methods:**
- `runAction($action_name, $params)` - Execute an action
- `renderTemplate($template_path, $data)` - Render template
- `halt($text)` - Stop execution with output
- `redirect($url)` - Redirect to URL
- `error404()` - Show 404 error

**Subclasses:**
- `cmsFrontend` - Frontend controllers (public pages)
- `cmsBackend` - Backend controllers (admin panel)

### cmsModel
Base model class for database operations.

**Key Constants:**
- `LEFT_JOIN`, `RIGHT_JOIN`, `INNER_JOIN` - Join types
- `READ_UNCOMMITTED`, `READ_COMMITTED`, etc. - Transaction isolation
- `DEFAULT_TABLE_PREFIX = 'con_'` - Content tables prefix
- `DEFAULT_TABLE_CATEGORY_POSTFIX = '_cats'` - Category table suffix

**Key Properties:**
- `$db` - Database instance
- `$table` - Current table name
- `$select` - SELECT fields
- `$where` - WHERE conditions
- `$join` - JOIN clauses
- `$order_by` - ORDER BY clause
- `$limit` - Query limit

**Key Methods:**
- `select($fields)` - Add SELECT fields
- `where($field, $value)` - Add WHERE condition
- `join($table, $condition, $type)` - Add JOIN
- `orderBy($field, $direction)` - Add ORDER BY
- `limit($limit, $offset)` - Set LIMIT
- `get()` - Execute query and return results
- `getItem()` - Get single item
- `insert($table, $data)` - Insert record
- `update($table, $data, $id)` - Update record
- `delete($table, $id)` - Delete record

### cmsDatabase
Database connection wrapper (mysqli).

**Key Properties:**
- `$prefix` - Table prefix
- `$query_count` - Query counter
- `$query_quiet` - Suppress errors if true

**Key Methods:**
- `getInstance()` - Get singleton
- `query($sql)` - Execute SQL query
- `escape($string)` - Escape string for SQL
- `affectedRows()` - Get affected rows count
- `insertId()` - Get last inserted ID
- `fetchAssoc($result)` - Fetch row as associative array
- `fetchRow($result)` - Fetch row as numeric array

### cmsForm
Base class for forms.

**Structure:**
```php
class formContentCategory extends cmsForm {
    public function init($ctype, $action) {
        $fieldsets = [
            'base' => [
                'title' => LANG_BASIC_OPTIONS,
                'type' => 'fieldset',
                'childs' => [
                    new fieldString('title', [
                        'title' => LANG_CATEGORY_TITLE,
                        'rules' => [['required']]
                    ]),
                    // ... more fields
                ]
            ]
        ];
        return $fieldsets;
    }
}
```

### cmsFormField
Base class for all field types.

**Key Properties:**
- `$name` - Field name
- `$title` - Field title for display
- `$sql` - SQL definition for database
- `$filter_type` - Filter type (false, 'like', 'eq', 'int', 'date')
- `$is_public` - Can be used in content types
- `$allow_index` - Allow database index

**Field Types** (in `system/fields/`):
- `string.php` - Single line text
- `text.php` - Multi-line text
- `html.php` - HTML content (with WYSIWYG)
- `number.php` - Numeric value
- `date.php` - Date/time picker
- `checkbox.php` - Boolean checkbox
- `list.php` - Dropdown select
- `listmultiple.php` - Multi-select
- `listbitmask.php` - Bitmask list
- `listgroups.php` - Groups list
- `image.php` - Single image upload
- `images.php` - Multiple images
- `file.php` - File upload
- `url.php` - URL field
- `user.php` - User selector
- `category.php` - Category selector
- `parent.php` - Parent item selector
- `child.php` - Child items selector
- `age.php` - Age field
- `color.php` - Color picker
- `captcha.php` - CAPTCHA
- `toolbar.php` - Toolbar field
- `fieldsgroup.php` - Group of fields
- `forms.php` - Form selector
- `hidden.php` - Hidden field
- `city.php` - City selector (geo)
- `paypal.php` - Payment field

### cmsGrid
Data grid builder for admin pages.

**Grid Options:**
```php
$grid = [
    'source_url' => '/admin/controller/get_data',
    'options' => [
        'order_by' => 'id',
        'order_to' => 'asc',
        'show_id' => true,
        'is_sortable' => true,
        'is_filter' => true,
        'is_pagination' => true,
        'perpage' => 30,
        'is_draggable' => false,
        'is_selectable' => false,
    ],
    'columns' => [
        [
            'title' => 'Title',
            'width' => 200,
            'href' => href_to('controller', 'edit', ['{id}']),
        ],
        // ...
    ],
    'actions' => [
        ['title' => 'Edit', 'href' => href_to('controller', 'edit', ['{id}'])],
        ['title' => 'Delete', 'href' => href_to('controller', 'delete', ['{id}']), 'class' => 'text-danger'],
    ]
];
```

Load grid in controller:
```php
$grid = $this->loadDataGrid('grid_name');
```

### cmsEventsManager
Event/hook system.

**Trigger Hook:**
```php
// Call hook and get modified data
$data = cmsEventsManager::hook('event_name', $data);

// Call all hooks, each receives original data
cmsEventsManager::hookAll('event_name', $data);

// Hook with default return value
$result = cmsEventsManager::hook('event_name', $data, $default_return);
```

**Core Events:**
- `core_start` - After bootstrap, before anything else
- `engine_start` - Template engine started
- `page_is_allowed` - Check page access
- `user_registered` - New user registered
- `user_login` - User logged in
- `user_logout` - User logged out
- `user_delete` - User deleted
- `content_added` - Content item created
- `content_after_add` - After content created
- `content_updated` - Content item updated
- `content_deleted` - Content item deleted
- `comment_add` - Comment added
- `html_filter` - Filter HTML content

### cmsRequest
HTTP request wrapper.

**Access:**
- `cmsCore::getInstance()->request` - Current request
- `$this->request` - In controllers

**Key Methods:**
- `get($key, $default)` - GET parameter
- `post($key, $default)` - POST parameter
- `getServer($key)` - Server variable
- `isAjax()` - AJAX request check
- `hasFiles()` - File uploads check

### cmsUser
User object.

**Key Properties:**
- `id` - User ID
- `email` - User email
- `nickname` - Display name
- `avatar` - Avatar path
- `is_admin` - Admin flag
- `is_logged` - Logged in flag

**Key Methods:**
- `get()` - Get current user
- `getId()` - Get user ID
- `isLogged()` - Check if logged in
- `isAdmin()` - Check if admin
- `isAllowed($controller, $type, $id)` - Check permission
- `addSessionMessage($message, $type)` - Add flash message

### cmsTemplate
Template engine.

**Key Methods:**
- `render($template_file, $data)` - Render template file
- `renderPartial($template_file, $data)` - Render without layout
- `getVar($key)` - Get template variable
- `setVar($key, $value)` - Set template variable

**Template Files:**
- Use `.tpl.php` extension
- Access template via `$phpfen` variable in templates
- URL helpers: `href_to()`, `href_to_abs()`, `uri_to()`

### cmsConfig
Configuration singleton.

**Access:** `cmsConfig::getInstance()` or `cmsConfig::get('key')`

**Key Config Values:**
- `db_host` - Database host
- `db_name` - Database name
- `db_user` - Database user
- `db_pass` - Database password
- `db_prefix` - Table prefix
- `language` - Default language
- `time_zone` - Server timezone
- `root_path` - Installation root
- `sitename` - Site name
- `debug` - Debug mode flag

### cmsCache
Caching abstraction.

**Usage:**
```php
cmsCache::getInstance()->start();
cmsCache::getInstance()->stop();
// Or use direct methods
```

**Cache Backends:**
- `cachefiles.php` - File-based cache
- `cachememcache.php` - Memcache
- `cachememcached.php` - Memcached
- `cacheredis.php` - Redis

---

## Hooks/Events System

Hooks are stored in `system/controllers/{controller}/hooks/` directory.

**File Naming:** `{event}_{action}.php` (e.g., `wall_after_add.php`, `content_after_add_approve.php`)

**Hook Class Structure:**
```php
class onUsersWallAfterAdd extends cmsAction {
    public function run($param1, $param2) {
        // Handler code
        return $result; // Optional return value
    }
}
```

**Hook Class Naming:** `on{Controller}{EventName}` → `on{Event}_{Action}`

**Example Hook File** (`wall_after_add.php`):
```php
class onUsersWallAfterAdd extends cmsAction {
    public function run($profile_type, $profile_id, $entry, $wall_model) {
        if ($profile_type != 'user') { return false; }
        // ... handler code
        return true;
    }
}
```

**Triggering Hooks:**
```php
// Single hook, data passes through chain
$result = cmsEventsManager::hook('wall_after_add', $data);

// All hooks, each gets original data, returns array of results
$results = cmsEventsManager::hookAll('wall_after_add', $data);

// Multiple events with same data
$data = cmsEventsManager::hook(['event1', 'event2'], $data);
```

---

## Controller Structure

### Frontend Controller
```php
class mycontroller extends cmsFrontend {
    const perpage = 15;

    public function route($uri) {
        // Custom routing logic
        $action_name = parent::parseRoute($this->cms_core->uri);
        if (!$action_name) {
            return cmsCore::error404();
        }
        $this->runAction($action_name);
    }

    public function actionIndex() {
        // Default action
    }
}
```

### Backend Controller
```php
class mycontroller extends cmsBackend {
    public function __construct($request) {
        parent::__construct($request);
        $this->root_url = '/admin/mycontroller';
    }

    public function getIndexComps() {
        return [
            'items' => 'My Items',
        ];
    }

    public function actionIndex() {
        // Admin list
    }
}
```

### Controller Directory Structure
```
system/controllers/{name}/
├── frontend.php              # Frontend controller class
├── backend.php                # Backend controller class (if admin)
├── model.php                  # Frontend model
├── routes.php                 # Custom routing rules
├── actions/                   # Action files
│   ├── item_view.php
│   ├── item_add.php
│   └── item_edit.php
├── backend/
│   ├── backend.php            # Backend controller (if separate)
│   └── model.php              # Backend model
├── forms/                     # Form classes
│   ├── form_item.php
│   └── form_options.php
├── hooks/                     # Event hooks
│   ├── engine_start.php
│   └── content_after_add.php
└── widgets/                   # Widget files
    └── widget_mylist.php
```

### Controller Actions
Actions are methods prefixed with `action` or files in `actions/` directory.

**Method-based action:**
```php
public function actionMyAction($param1, $param2) {
    // Returns string or cmsResponse
}
```

**File-based action** (`actions/my_action.php`):
```php
public function run($param1, $param2) {
    // Action code
}
```

---

## Model Structure

### Frontend Model
```php
class modelContent extends cmsModel {
    const DEFAULT_TABLE_PREFIX = 'con_';

    public function getContentTypes() {
        return $this->get('content_types')->fetchAll();
    }

    public function getContentType($id) {
        return $this->get('content_types')->where('id', $id)->fetchOne();
    }
}
```

### Backend Model
```php
class modelBackendContent extends cmsModel {
    public function getContentTypes() {
        return $this->get('content_types')->fetchAll();
    }
}
```

---

## Form Structure

Forms extend `cmsForm` and define structure in `init()` method.

```php
class formMyItem extends cmsForm {
    public function init($options = []) {
        return [
            'basic' => [
                'title' => LANG_BASIC_OPTIONS,
                'type' => 'fieldset',
                'childs' => [
                    new fieldString('title', [
                        'title' => LANG_TITLE,
                        'rules' => [
                            ['required'],
                            ['max_length', 255]
                        ]
                    ]),
                    new fieldText('description', [
                        'title' => LANG_DESCRIPTION
                    ]),
                    new fieldCheckbox('is_active', [
                        'title' => LANG_IS_ACTIVE,
                        'default' => true
                    ])
                ]
            ]
        ];
    }
}
```

**Field Options:**
- `title` - Field display title
- `hint` - Help text
- `default` - Default value
- `rules` - Validation rules array
- `options` - Field-specific options
- `generator` - Callback for dynamic options
- `disabled` - Disable field

**Validation Rules:**
```php
['required']
['max_length', 255]
['min_length', 3]
['email']
['url']
['regexp', '/^[a-z]+$/']
['custom_rule', $param]
```

---

## Language Files

### Core Language File
`system/languages/{lang}/language.php`
```php
define('LANG_LOADING', 'Загрузка...');
define('LANG_SENDING', 'Отправка...');
define('LANG_TITLE', 'Заголовок');
define('LANG_CONTENT_TYPE', 'Тип контента');
```

### Controller Language File
`system/languages/{lang}/controllers/{name}/content.php`
```php
<?php
    define('LANG_MYCONTROLLER_TITLE', 'My Controller');
    define('LANG_MYCONTROLLER_ITEM', 'Item');
    // ...
```

### Usage in Code
```php
LANG_TITLE                  // Direct constant
LANG_CONTENT_ADD_ITEM       // With sprintf placeholders
sprintf(LANG_CONTENT_ADD_ITEM, $ctype['labels']['create'])
```

---

## URL Generation

```php
href_to($controller, $action, $params)      // Generate URL
href_to_abs($controller, $action, $params)  // Absolute URL
uri_to($uri)                                 // URI to URL
```

**Examples:**
```php
href_to('content', 'item', [123])           // /content/item/123.html
href_to('users', 'profile', [$user_id])     // /users/profile/123.html
href_to_abs('content', 'item', [123])       // http://site.com/content/item/123.html
```

---

## Template System

Templates are in `templates/{name}/` with `.tpl.php` files.

### Template Variables
- `$phpfen` - Template object
- `$request` - Current request
- `$user` - Current user
- `$cms_config` - Site config

### Template Functions
```php
// URL generation
<?php echo href_to('controller', 'action', $params); ?>

// Conditional
<?php if ($item['is_active']): ?>
    <span>Active</span>
<?php endif; ?>

// Loop
<?php foreach ($items as $item): ?>
    <div><?php html($item['title']); ?></div>
<?php endforeach; ?>

// Widget inclusion
<?php echo $phpfen->renderWidget('widget_name', $options); ?>
```

---

## Widget System

Widgets are in `system/controllers/{name}/widgets/` or `templates/{name}/widgets/`.

### Widget Structure
```php
class widgetMyList extends cmsWidget {
    public function run($options) {
        $model = cmsCore::getModel('content');
        $items = $model->getItems($options['limit'] ?? 10);
        
        return [
            'items' => $items,
            'options' => $options
        ];
    }
}
```

### Widget Templates
`templates/{name}/widgets/{widget_name}.tpl.php`

---

## Database Tables

**Core Tables:**
- `cms_users` - User accounts
- `cms_user_tokens` - Auth tokens
- `cms_user_sessions` - User sessions
- `cms_user_warnings` - User warnings
- `cms_content_types` - Content type definitions
- `cms_content_items` - Content items base table
- `cms_con_{ctype}_fields` - Per-content-type fields
- `cms_categories` - Categories (nested sets)
- `cms_comments` - Comments
- `cms_ratings` - Ratings
- `cms_tags` - Tags
- `cms_tags_items` - Tag relations
- `cms_widgets` - Widget instances
- `cms_widgets_pages` - Page-widget bindings
- `cms_menus` - Menu definitions
- `cms_menu_items` - Menu items
- `cms_events` - Event subscriptions
- `cms_controllers` - Registered controllers
- `cms_permissions` - Permission rules
- `cms_sessions` - PHP sessions storage
- `cms_cache` - Cache table

---

## Build, Test, and Lint Commands

**No formal testing framework is set up.** No PHPUnit, phpunit.xml, or tests directory.

### PHP Syntax Check
```bash
php -l <file.php>
```

### Code Formatting
- **4-space indentation** (not tabs)
- No automated formatter configured

### Static Analysis
- None configured (no phpstan, phan, etc.)

---

## Code Style Guidelines

### PHP Tag and Encoding
- Always use `<?php` (never `<?`)
- Files must be UTF-8 encoded
- Always include closing `?>` tag (except pure class files)
- Add `#[\AllowDynamicProperties]` attribute to classes using dynamic properties

### Indentation
- Use **4 spaces** for indentation
- Section dividers: `//============================================================================//`

### Naming Conventions
| Element | Convention | Example |
|---------|-----------|---------|
| Classes | CamelCase | `cmsController`, `cmsDatabase` |
| Methods | camelCase | `getInstance()`, `setOptions()` |
| Properties | camelCase | `$table_prefix`, `$query_count` |
| Constants | UPPER_SNAKE_CASE | `LEFT_JOIN`, `READ_COMMITTED` |
| Core classes | `cms` prefix | `cmsDatabase`, `cmsController` |
| Field classes | `field` prefix | `fieldString`, `fieldText` |
| Model classes | `model` prefix | `modelContent`, `modelBackendContent` |
| Hook classes | `on` prefix | `onUsersWallAfterAdd` |
| Namespaces | `icms\` prefix | `icms\traits\corePropertyLoadable` |
| Table names | snake_case | `content_items`, `user_sessions` |

### PHPDoc and Comments
- Use **Russian comments** for internal documentation
- PHPDoc format:
```php
/**
 * Description of method
 * @param string $param_name Description
 * @return array|null
 * @throws Exception
 */
```
- Property doc format:
```php
/**
 * Description of property
 * @var string
 */
public $property_name;
```

### Error Handling
- Use `Exception` class (not custom exceptions unless necessary)
- `throw new Exception('Error message')`
- `cmsCore::error404()` for page not found
- Set `$this->query_quiet = true` on database object to suppress query errors

### Best Practices
1. Use `cmsDatabase::getInstance()` for DB connection
2. Use `cmsCore::getInstance()` for core access
3. Check `$this->cms_config->debug` before debug output
4. Use `cmsUser::isAllowed()` for permission checks
5. Use `cmsObject::merge()` for combining options arrays
6. Use lang constants instead of hardcoded strings
7. Use prepared statements for SQL queries

---

## Important Constants

```php
PATH                    // Root path (set in bootstrap.php)
ROOT                    // Document root (legacy)
ICMS_CONFIG_DIR         // Config directory
VALID_RUN               // HTTP mode flag (timestamp)
SESSION_START           // Session needed flag
```

---

## Quick Reference

### Getting Started with New Controller
1. Create directory: `system/controllers/{name}/`
2. Create `frontend.php` extending `cmsFrontend`
3. Create `model.php` extending `cmsModel`
4. Create `routes.php` for custom URLs (optional)
5. Create `actions/` directory for file-based actions
6. Create `hooks/` directory for event hooks
7. Create `forms/` directory for form classes
8. Create language file: `system/languages/ru/controllers/{name}/content.php`
9. Create manifest: `install/manifests/{name}.php`

### Getting Started with New Field Type
1. Create file: `system/fields/{fieldname}.php`
2. Extend `cmsFormField` class
3. Implement required methods: `parse()`, `getSQL()`, `applyFilter()`
4. Register in content type via admin panel

---

## Installation System

The `install/` directory contains the web-based installation wizard and CLI rebuild script.

### Directory Structure
```
install/
├── index.php              # Main entry point
├── functions.php          # Helper functions
├── manifest.php           # Build configuration
├── rebuild.php            # CLI distribution builder
├── steps/                 # Installation step handlers
│   ├── start.php         # Welcome screen
│   ├── license.php        # GPL license acceptance
│   ├── php.php            # PHP requirements check
│   ├── paths.php          # Path configuration
│   ├── site.php           # Site settings (name, template)
│   ├── database.php       # DB connection, table creation, SQL import
│   ├── admin.php          # Admin user creation
│   ├── config.php         # Config file generation
│   ├── cron.php           # Cron configuration
│   ├── addons.php         # External addons selection
│   ├── addons_install.php # External addons installation (after bootstrap)
│   └── finish.php         # Completion
├── manifests/             # Per-controller file lists for uninstall
│   ├── billing.php
│   ├── comments.php
│   └── ...
├── templates/             # Step HTML templates
│   ├── main.php          # Main layout wrapper
│   └── step_*.php        # Individual step templates
├── languages/             # Localization
│   ├── ru/
│   │   ├── language.php  # Russian strings
│   │   └── sql/          # SQL dumps
│   │       ├── base.sql           # Core tables
│   │       ├── base_demo_*.sql    # Demo content
│   │       ├── widgets_bind_*.sql # Widget layouts
│   │       └── packages/          # Per-controller SQL
│   └── en/
├── css/, js/, images/    # Frontend assets
├── upload/                # Default uploaded files per template
└── externals/             # Downloaded external addons
```

### Installation Flow
1. **Session-based navigation** - Each step stores data in `$_SESSION['install']`
2. **AJAX-driven** - Steps submitted via XMLHttpRequest, responses JSON
3. **11 total steps** - Plus optional addons step if `externals/` directory exists
4. **SQL Dumps** - `base.sql` creates core tables, `packages/*/` for controllers
5. **Config generation** - PHP config file written to `system/config/config.php`
6. **Admin creation** - User ID 1 updated with credentials + auth token

### Step Files
Each step file (`steps/{step}.php`) must define:
```php
function step($is_submit) {
    // $is_submit indicates form submission vs initial display
    return [
        'html' => render('step_template_name', $data),
        'error' => false,
        'message' => 'optional error message'
    ];
}
```

### Key Helper Functions (`functions.php`)
```php
render($template_name, $data)      // Simple template renderer (output buffering)
run_step($step, $is_submit)        // Includes step file, calls step()
get_db_list()                       // Lists MySQL databases
import_dump($mysqli, $file, $prefix, $engine, $delimiter, $charset, $innodb_full_text)
                                     // Generator-based SQL parser with prefix replacement
copy_folder($dir_source, $dir_target) // Recursive directory copy
preinstall_addon($addon)            // Downloads and extracts addon from API
get_api_method($name, $params)     // CURL wrapper for instantcms.ru API
```

### SQL Structure
```
languages/{lang}/sql/
├── base.sql                    # Core tables (users, content, widgets, etc.)
├── base_demo_{template}.sql    # Demo content data
├── widgets_bind_{template}.sql  # Default widget positions
├── widgets_bind_demo_{template}.sql
└── packages/
    ├── billing/
    ├── comments/
    ├── forms/
    └── ...
```

### Manifest Files (`install/manifests/*.php`)
Define which files/dirs belong to each addon for uninstall:
```php
return [
    'dirs' => [
        'system/controllers/billing',
        'system/languages/ru/controllers/billing',
    ],
    'files' => [
        'system/languages/ru/letters/billing_*.txt',
    ]
];
```

### Rebuild Script (`rebuild.php`)
CLI tool to create custom InstantCMS distributions:
```bash
php -f rebuild.php
```

Features:
- Removes components listed in `manifest['removed']`
- Adds paid/free addons from API via `manifest['added']`
- Sets file permissions (644 files, 755 dirs)
- Creates ZIP archive
- Marks as custom: `is_custom = 1` in version.ini

### Build Manifest (`install/manifest.php`)
Configuration for archive building:
```php
return [
    'removed' => ['billing', 'photos'],    // Components to exclude
    'create_archive' => true,               // Create ZIP
    'archive_name' => 'my_custom_build',   // Archive filename
    'added' => ['[addon]123[/addon]']       // Addons to include from API
];
```

### Addons API
- **Endpoint**: `https://api.instantcms.ru/{lang}api/method/`
- **API Key**: `8e13cb202f8bdc27dc765e0448e50d11` (hardcoded)
- Downloads addon ZIP, extracts to `externals/`, copies `package/` to site root

### Config Generation (`steps/config.php`)
Writes `system/config/config.php`:
```php
$config = [
    'root' => '/',
    'db_host' => 'localhost',
    'db_base' => 'instantcms',
    'db_user' => 'root',
    'db_pass' => '',
    'db_prefix' => 'cms_',
    'language' => LANG,
    'template' => 'default',
    'debug' => 0,
    // ... 100+ options
];
write_config($file, $config);
```

### Admin Creation (`steps/admin.php`)
Updates User ID 1 with:
- Nickname, email, bcrypt password hash
- Auth token (SHA512 of md5's)
- Auto-login via cookie
