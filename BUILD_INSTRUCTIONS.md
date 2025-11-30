# How to Build CSS Without Running `npm run dev`

This guide explains how to compile your Tailwind CSS so it works without needing to run `npm run dev` continuously.

## Quick Start

1. **Install dependencies** (if not already done):
   ```bash
   npm install
   ```

2. **Build the assets**:
   ```bash
   npm run build
   ```

3. **That's it!** Your CSS is now compiled and will work without `npm run dev`.

## How It Works

### Development Mode (with `npm run dev`)
- Run `npm run dev` to start the Vite development server
- Changes to CSS/JS are automatically reflected (Hot Module Replacement)
- Requires the dev server to be running

### Production Mode (with `npm run build`)
- Run `npm run build` to compile all assets
- Compiled files are saved to `public/build/` directory
- Laravel automatically uses these compiled files when dev server is not running
- No need to keep `npm run dev` running

## When to Rebuild

You need to run `npm run build` again when you:
- Add new Tailwind CSS classes
- Modify `resources/css/app.css`
- Change JavaScript files in `resources/js/`
- Update Tailwind configuration

## File Structure

After building, you'll see:
```
public/
  └── build/
      ├── assets/
      │   ├── app-COL2P-2K.css    (Your compiled Tailwind CSS)
      │   ├── app-D-FaURHc.js     (Your compiled JavaScript)
      │   └── ...
      └── manifest.json            (Tells Laravel which files to use)
```

## Troubleshooting

### CSS not updating after build?
1. Clear browser cache (Ctrl+Shift+R or Cmd+Shift+R)
2. Make sure you ran `npm run build` after making changes
3. Check that `public/build/` directory exists and has files

### Build fails?
1. Make sure all dependencies are installed: `npm install`
2. Check for errors in the terminal output
3. Verify `package.json` has the build script: `"build": "vite build"`

### Want to see changes immediately during development?
- Use `npm run dev` for development (with hot reload)
- Use `npm run build` for production (compiled, no server needed)

## Commands Reference

```bash
# Install dependencies (first time only, or after package.json changes)
npm install

# Build for production (compile assets)
npm run build

# Development mode (with hot reload - requires server running)
npm run dev
```

## Notes

- The `@vite()` directive in your Blade templates automatically detects whether to use dev server or compiled assets
- You don't need to change any code - Laravel handles this automatically
- Compiled assets are optimized and minified for production







