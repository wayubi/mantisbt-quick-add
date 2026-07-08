# MantisBT Quick Add Task Plugin

## Description

This plugin adds a global keyboard shortcut to MantisBT that allows users to quickly create a new task from any page:

- **Press `Q`**: Opens a modal dialog to add a task
- **Project Selection**: Choose the project from a dropdown
- **Category Loading**: Categories load dynamically via AJAX when the project changes
- **Due Date/Time**: Set an optional due date and time

## Features

- **Global Hotkey**: Press `Q` to open the quick add modal from any page
- **Modal Form**: Bootstrap-based modal for quick task entry
- **Project Selector**: Dropdown of accessible projects with report permission
- **Dynamic Categories**: Categories load automatically when project changes (AJAX)
- **Due Date & Time**: Built-in date and time pickers with defaults (today at noon)
- **Summary & Description**: Core fields with summary required and max 128 characters
- **Permission Checks**: Only users with report permission can access the feature
- **Auto-Focus**: Summary field is automatically focused when modal opens
- **Input-Aware Hotkey**: Hotkey is disabled when focus is in input/textarea/select fields
- **Internationalization**: Supports multiple languages via language strings

## Requirements

- MantisBT 2.0.0 or higher
- PHP 5.6 or higher

## Installation

1. Download the plugin files
2. Extract the `QuickAdd` folder
3. Upload the entire `QuickAdd` folder to your MantisBT `plugins` directory
4. Log in to MantisBT as an administrator
5. Navigate to **Manage** → **Manage Plugins**
6. Find "Quick Add Task" in the available plugins list
7. Click **Install**

## File Structure

```
QuickAdd/
├── QuickAdd.php                  # Main plugin file
├── pages/
│   ├── quick_add.php             # Form handler - creates the bug
│   └── quick_add_ajax.php        # AJAX endpoint - loads categories by project
├── files/
│   └── quick_add.js              # JavaScript: hotkey, modal, category loading
├── lang/
│   └── strings_english.txt       # Language strings
├── LICENSE                       # MIT License
└── README.md                     # This file
```

## Usage

### Adding a Task from Any Page

1. Press the **`Q`** key while viewing any page in MantisBT (not focused on an input field)
2. A modal dialog titled "Quick Add Task" appears
3. Select a **Project** from the dropdown — the category list updates automatically
4. Select a **Category** (required)
5. Enter a **Summary** (required, max 128 characters)
6. Optionally enter a **Description**
7. Optionally set a **Due Date** and **Time** (defaults to today at 12:00)
8. Click **Create Task** to submit

The plugin creates the issue and redirects you to the new issue's view page.

## How It Works

1. User presses `Q` (not in an input/textarea/select)
2. The Bootstrap modal is displayed
3. The modal auto-populates the project selector with accessible projects
4. When the project changes, an AJAX request loads categories for that project
5. User fills in the form and submits
6. The plugin:
   - Validates the form security token
   - Checks the user has report permission for the selected project
   - Creates the bug with the provided details
   - Sets the due date if the user has `due_date_update_threshold` permission
   - Sends email notification
   - Redirects to the new issue view page

## Example

Pressing `Q` from the main dashboard:

1. Modal opens with project selector defaulting to the first accessible project
2. Categories load automatically
3. User enters "Fix login button alignment" as summary
4. User sets due date to next Friday
5. User clicks **Create Task**
6. Issue is created and user is redirected to view the new issue

## Permissions

Only users with permission to report issues (based on the `report_bug_threshold` configuration) can access the quick add modal. The due date field is only saved if the user has `due_date_update_threshold` permission.

## Internationalization

Language strings can be customized in the `lang/strings_english.txt` file or by adding language-specific files (e.g., `strings_french.txt`).

Customizable strings include:
- Plugin title and description
- Modal title and submit button text
- Error messages

## Notes

- The `Q` hotkey is disabled when focus is in an input, textarea, or select element
- The summary field is automatically focused when the modal opens
- If no description is provided, a single period (`.`) is used as the description
- Due date defaults to today at 12:00 noon
- Category list is only populated after a project is selected
- The plugin uses MantisBT's native Bootstrap-based modal and form styling

## Troubleshooting

**Q: Pressing Q doesn't open the modal**
- Ensure you are not focused on an input, textarea, or select field
- Verify you have permission to report issues in at least one project
- Check that the plugin is installed and enabled
- Look for JavaScript errors in your browser's developer console

**Q: The category dropdown is empty**
- Ensure the selected project has categories defined
- Verify the AJAX request completes successfully (check browser's Network tab)

**Q: The due date isn't saved**
- Verify you have `due_date_update_threshold` permission for the project
- Check the date/time format is valid

## Support

For issues or questions, please refer to the MantisBT documentation or community forums.

## License

This plugin is licensed under the MIT License. See the `LICENSE` file for details.

## Version History

**1.0** - Initial release
- Global `Q` keyboard shortcut to open quick add modal
- Dynamic project and category selection via AJAX
- Due date and time support with permission checks
- Bootstrap modal integration with MantisBT styling
- Internationalization support via language strings
- Input-aware hotkey (disabled in form fields)
- Email notification on task creation
