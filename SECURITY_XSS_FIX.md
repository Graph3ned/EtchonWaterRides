# XSS Vulnerability – Script Injection & Email Harvesting

## What Happened

A script was injected in a **public** (or otherwise attacker-controlled) place. When staff or other users viewed the Staff Dashboard, that script ran in their browser and could **gather email addresses** (e.g. from the layout/navigation that shows the logged-in user’s email) and send them to the attacker.

## Root Cause

The vulnerability was in **`resources/views/livewire/staff-dashboard.blade.php`**.

1. **Use of `innerHTML` with user/data-derived content**  
   The alarm modal was filled using:
   - `rideType`, `classification`, and `identification` read from the table (from the DOM via `textContent`).
   - Those values were then interpolated into an HTML string and written with **`innerHTML`**.

2. **Why this is exploitable**  
   Ride type name, classification name, and identifier can come from the database (e.g. created via “Add Rental” or admin/ride management). Even though Blade escapes them when rendering the table (`{{ }}`), the JavaScript then:
   - Reads that **text** from the DOM (so the attacker’s payload is present as a string).
   - Writes it into the modal with **`innerHTML`**, so the browser **parses it as HTML** and runs any `<script>` or event handlers (e.g. `onerror` on `<img>`).

3. **How emails were gathered**  
   Once the attacker’s script runs in the victim’s browser, it can:
   - Read the DOM (e.g. the user’s email in the navigation).
   - Send that data to the attacker’s server (e.g. via `fetch()` to an external URL).

So the **vulnerability** was: **Stored / reflected XSS via `innerHTML`** in the staff dashboard alarm modal, which allowed a script to run and harvest data such as emails.

## Fix Applied

- **Stopped using `innerHTML`** for the alarm modal message.
- The modal message is now set with **`textContent`** only, and the message is built by **string concatenation** (no HTML interpolation).
- As a result, ride type, classification, and identification are **never** interpreted as HTML, so injected script cannot execute.

## Files Changed

- `resources/views/livewire/staff-dashboard.blade.php`
  - `testTimeOut()`: modal message set with `textContent`; message is plain text.
  - `checkAlarms()`: same — message built as plain text and set with `textContent`.

## Recommendations

1. **Input validation**  
   Continue to validate and sanitize ride type names, classification names, identifiers, and notes (length, allowed characters) on the server.

2. **Avoid `innerHTML` for user/data-driven content**  
   Prefer `textContent` or a safe templating/sanitization approach. If you must use HTML, use a vetted sanitizer (e.g. DOMPurify) and a strict policy.

3. **Content Security Policy (CSP)**  
   Consider adding a CSP header to reduce the impact of any future XSS (e.g. restrict inline script and only allow trusted sources).

4. **Audit**  
   Search the project for other uses of `innerHTML`, `document.write`, or `eval()` with any user or database-sourced data and fix or remove them.
