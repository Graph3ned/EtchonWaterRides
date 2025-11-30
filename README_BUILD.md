# Quick Build Guide

## For Your Friend - Simple Steps

### Option 1: Use the Batch File (Easiest)
1. Double-click `build-assets.bat`
2. Wait for it to finish
3. Done! Your CSS will now work without `npm run dev`

### Option 2: Manual Command
Open PowerShell or Command Prompt in the project folder and run:
```bash
npm run build
```

## What This Does

- Compiles all Tailwind CSS into a single optimized file
- Compiles all JavaScript files
- Saves everything to `public/build/` folder
- Laravel automatically uses these files (no code changes needed!)

## When to Rebuild

Run the build again when you:
- ✅ Add new CSS classes
- ✅ Change Tailwind styles
- ✅ Modify JavaScript files
- ✅ Update any frontend code

## Important Notes

- **First time?** Make sure `npm install` has been run first
- **After building:** Your CSS works immediately - no need to run `npm run dev`
- **During development:** You can still use `npm run dev` if you want live updates

## Troubleshooting

**Build fails?**
- Run `npm install` first
- Make sure you're in the project root folder

**CSS not showing?**
- Clear browser cache (Ctrl+Shift+R)
- Make sure `public/build/` folder exists with files inside

**Need help?**
- Check `BUILD_INSTRUCTIONS.md` for detailed information







