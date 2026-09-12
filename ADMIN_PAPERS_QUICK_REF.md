# Admin Papers Feature — Quick Reference

## Routes

| Route | Method | Purpose |
|-------|--------|---------|
| `/admin/users/{user}/papers` | GET | List all papers for user |
| `/admin/users/{user}/papers/create` | GET | Paper type selector |
| `/admin/users/{user}/papers` | POST | Create paper |
| `/admin/users/{user}/papers/{paper}` | GET | A4 preview |
| `/admin/users/{user}/papers/{paper}/edit` | GET | Redirect to SPA |
| `/admin/users/{user}/papers/{paper}` | PUT | Update metadata |
| `/admin/users/{user}/papers/{paper}` | DELETE | Delete paper |
| `/admin/users/{user}/papers/{paper}/status` | PATCH | Toggle status |
| `/admin/users/{user}/papers/{paper}/export` | GET | Download JSON |

## API Endpoints

| Endpoint | Method | Purpose |
|----------|--------|---------|
| `/api/admin/users/{user}/papers` | GET | List papers (JSON) |
| `/api/admin/users/{user}/papers` | POST | Create paper |
| `/api/admin/users/{user}/papers/{paper}` | GET | Fetch paper JSON |
| `/api/admin/users/{user}/papers/{paper}` | PUT | Update paper |

## SPA Launchers

```
/admin/spa/manual?paper_id={id}&target_user={user_id}
/admin/spa/auto?paper_id={id}&target_user={user_id}
```

Both inject admin context:
- `window.adminTargetUserId = {user_id}`
- `window.adminTargetUserName = "{user_name}"`

## View URLs

| View | URL | Purpose |
|------|-----|---------|
| User Papers List | `/admin/users/{user}/papers` | Manage all papers for user |
| Create Paper | `/admin/users/{user}/papers/create` | New paper form |
| Paper Preview | `/admin/users/{user}/papers/{paper}` | A4 render view |

## Controllers

### AdminUserPaperController
- Handles Blade CRUD operations
- Protects all methods with `['auth', 'admin']` middleware
- Verifies `$paper->user_id === $user->id` in all methods

### AdminSpaApiController
- Handles SPA JSON API calls
- Protects with `['auth', 'admin']` middleware
- Verifies admin status before any operation

## Key Features

✅ **Full CRUD** — Create, read, update, delete papers for any user
✅ **No Impersonation** — Admin stays logged in as admin
✅ **Authorization** — Every operation checks paper ownership
✅ **Admin Banner** — Red warning banner when editing in SPA
✅ **Status Toggle** — Draft ↔ Published via AJAX
✅ **Paper Export** — Download paper as JSON
✅ **A4 Preview** — View formatted paper before publish
✅ **SweetAlert2** — Confirmation dialogs for destructive actions
✅ **User Integration** — Admin users list shows paper count + link

## Security Model

- All routes require `auth` + `admin` middleware
- Every controller method verifies paper belongs to target user
- API endpoints check `Auth::user()->isAdmin()` and paper ownership
- Route model binding ensures only valid users/papers are accessed
- Non-admins get 403, wrong user_id gets 404

## Development Notes

**Authorization Pattern** (used in all methods):
```php
private function verifyOwnership(UserPaper $paper, User $user): void
{
    if ($paper->user_id !== $user->id) {
        abort(404);
    }
}
```

**API Endpoint Selection** (in stores):
```javascript
const apiUrl = window.adminTargetUserId
    ? `/api/admin/users/${window.adminTargetUserId}/papers/{id}`
    : `/api/user/papers/{id}`;
```

**Admin Context Detection** (in App.vue):
```javascript
const isAdminContext = computed(() => !!window.adminTargetUserId);
```

## Files Reference

### Controllers
- `app/Http/Controllers/AdminUserPaperController.php` — 9 methods, 180 lines
- `app/Http/Controllers/AdminSpaApiController.php` — 3 methods, 110 lines

### Views
- `resources/views/admin/users/papers/index.blade.php` — Papers list table
- `resources/views/admin/users/papers/create.blade.php` — Paper creation form
- `resources/views/admin/users/papers/show.blade.php` — A4 preview

### Vue Components
- `resources/js/components/AdminContextBanner.vue` — Admin warning banner

### Modified Files
- `routes/web.php` — Routes for admin papers CRUD + SPA launchers + API
- `app.blade.php` — Window variables for admin context
- `App.vue` — Banner rendering + admin context detection
- `TopBar.vue` — Admin API endpoint support
- `autoPaperStore.js` — Admin API endpoint support
- `admin/users/index.blade.php` — Papers column + link

## Testing Tips

1. Create test user via `/admin/users/create`
2. Navigate to user's papers: `/admin/users/{id}/papers`
3. Create manual paper → verify SPA loads with red banner
4. Edit paper → save → verify changes persisted
5. Create auto paper → add MCQs → save
6. View paper preview → verify A4 render
7. Delete paper → confirm dialog → verify removed
8. Toggle status → verify draft ↔ published
9. Export paper → verify JSON downloaded

## Common Errors & Solutions

| Error | Solution |
|-------|----------|
| 403 Unauthorized | Not logged in as admin |
| 404 Not Found | Paper doesn't exist or belongs to different user |
| CSRF token missing | Make sure form/AJAX includes CSRF token |
| Admin banner not showing | Check `window.adminTargetUserId` is set in browser console |
| API returns 404 | Paper ownership mismatch — verify `user_id` in database |
