# Rides Management Test Cases

| No. | Modules / Unit | Test Case | Steps | Cases |
|-----|----------------|-----------|-------|-------|
| 1 | Rides Management | View Rides Rate Page | 1. Login as admin; 2. Navigate to Rides Rate; 3. View page | Expected: Rides rate page loads with all ride types and classifications |
| 2 | | Add New Ride Type (Valid) | 1. Admin → Rides Rate; 2. Click Add New Ride; 3. Enter valid details; 4. Click Save | Ride Type: "Kayak"; Classifications: [{"name": "Single", "price": 100, "identifiers": ["red", "blue"]}]; Expected: Ride type added successfully |
| 3 | | Add New Ride Type (Duplicate Name) | 1. Admin → Rides Rate; 2. Click Add New Ride; 3. Enter existing name; 4. Click Save | Ride Type: "Existing Kayak"; Expected: Error - ride type already exists |
| 4 | | Add New Ride Type (Empty Name) | 1. Admin → Rides Rate; 2. Click Add New Ride; 3. Leave name empty; 4. Click Save | Ride Type: (empty); Expected: Validation error - name is required |
| 5 | | Add New Ride Type (Invalid Price) | 1. Admin → Rides Rate; 2. Click Add New Ride; 3. Enter negative price; 4. Click Save | Price: -100; Expected: Validation error - price must be positive |
| 6 | | Add New Ride Type (Duplicate Classification) | 1. Admin → Rides Rate; 2. Click Add New Ride; 3. Enter duplicate classification name; 4. Click Save | Classification: "Duplicate Name"; Expected: Error - classification already exists |
| 7 | | Add New Ride Type (Duplicate Identifier) | 1. Admin → Rides Rate; 2. Click Add New Ride; 3. Enter duplicate identifier; 4. Click Save | Identifier: "red" (already exists); Expected: Error - identifier already exists |
| 8 | | Restore Soft Deleted Ride Type | 1. Admin → Rides Rate; 2. Click Add New Ride; 3. Enter name of soft deleted ride type; 4. Click Save | Ride Type: "Deleted Kayak"; Expected: Soft deleted ride type restored, old data soft deleted |
| 9 | | View Ride Type Details | 1. Admin → Rides Rate; 2. Click View Details on any ride type; 3. View details page | Expected: Ride type details displayed with all classifications and identifiers |
| 10 | | Edit Ride Type (Valid) | 1. Admin → View Details; 2. Click Edit Ride Type; 3. Modify name; 4. Click Update | Name: "Updated Kayak"; Expected: Ride type name updated successfully |
| 11 | | Edit Ride Type (Duplicate Name) | 1. Admin → View Details; 2. Click Edit Ride Type; 3. Enter existing name; 4. Click Update | Name: "Existing Name"; Expected: Error - name already exists |
| 12 | | Delete Ride Type | 1. Admin → View Details; 2. Click Delete Ride Type; 3. Confirm deletion; 4. Verify | Expected: Ride type soft deleted with all classifications and identifiers |
| 13 | | Add Classification (Valid) | 1. Admin → View Details; 2. Click Add Classification; 3. Enter valid details; 4. Click Save | Classification: "Double Kayak"; Price: 150; Identifiers: ["yellow", "green"]; Expected: Classification added successfully |
| 14 | | Add Classification (Duplicate Name) | 1. Admin → View Details; 2. Click Add Classification; 3. Enter existing name; 4. Click Save | Classification: "Existing Classification"; Expected: Error - classification already exists |
| 15 | | Add Classification (Empty Name) | 1. Admin → View Details; 2. Click Add Classification; 3. Leave name empty; 4. Click Save | Classification: (empty); Expected: Validation error - name is required |
| 16 | | Add Classification (Invalid Price) | 1. Admin → View Details; 2. Click Add Classification; 3. Enter negative price; 4. Click Save | Price: -50; Expected: Validation error - price must be positive |
| 17 | | Add Classification (Duplicate Identifier) | 1. Admin → View Details; 2. Click Add Classification; 3. Enter duplicate identifier; 4. Click Save | Identifier: "red" (already exists); Expected: Error - identifier already exists |
| 18 | | Restore Soft Deleted Classification | 1. Admin → View Details; 2. Click Add Classification; 3. Enter name of soft deleted classification; 4. Click Save | Classification: "Deleted Classification"; Expected: Soft deleted classification restored |
| 19 | | Edit Classification (Valid) | 1. Admin → View Details; 2. Click Edit Classification; 3. Modify details; 4. Click Update | Name: "Updated Classification"; Price: 200; Expected: Classification updated successfully |
| 20 | | Edit Classification (Duplicate Name) | 1. Admin → View Details; 2. Click Edit Classification; 3. Enter existing name; 4. Click Update | Name: "Existing Classification"; Expected: Error - name already exists |
| 21 | | Edit Classification (Empty Name) | 1. Admin → View Details; 2. Click Edit Classification; 3. Leave name empty; 4. Click Update | Name: (empty); Expected: Validation error - name is required |
| 22 | | Edit Classification (Invalid Price) | 1. Admin → View Details; 2. Click Edit Classification; 3. Enter negative price; 4. Click Update | Price: -75; Expected: Validation error - price must be positive |
| 23 | | Delete Classification | 1. Admin → View Details; 2. Click Delete Classification; 3. Confirm deletion; 4. Verify | Expected: Classification soft deleted with all identifiers |
| 24 | | Add Identifier (Valid) | 1. Admin → Edit Classification; 2. Enter new identifier; 3. Click Add; 4. Verify | Identifier: "purple"; Expected: Identifier added to database successfully |
| 25 | | Add Identifier (Duplicate) | 1. Admin → Edit Classification; 2. Enter existing identifier; 3. Click Add; 4. Verify | Identifier: "red" (already exists); Expected: Error - identifier already exists |
| 26 | | Add Identifier (Empty) | 1. Admin → Edit Classification; 2. Leave identifier empty; 3. Click Add; 4. Verify | Identifier: (empty); Expected: Error - identifier cannot be empty |
| 27 | | Toggle Identifier Status (Active to Inactive) | 1. Admin → Edit Classification; 2. Click Active button; 3. Verify status change | Expected: Identifier status changed to Inactive in database |
| 28 | | Toggle Identifier Status (Inactive to Active) | 1. Admin → Edit Classification; 2. Click Inactive button; 3. Verify status change | Expected: Identifier status changed to Active in database |
| 29 | | Delete Identifier (Valid) | 1. Admin → Edit Classification; 2. Click Delete button; 3. Confirm deletion; 4. Verify | Expected: Identifier soft deleted from database |
| 30 | | Delete Identifier (Last Identifier) | 1. Admin → Edit Classification; 2. Delete last remaining identifier; 3. Verify | Expected: Error - cannot delete last identifier, empty field added |
| 31 | | Remove Empty Identifier Field | 1. Admin → Edit Classification; 2. Click X button on empty field; 3. Verify | Expected: Empty field removed from form |
| 32 | | Add New Identifier Field | 1. Admin → Edit Classification; 2. Click Add Identifier button; 3. Verify | Expected: New empty identifier field added to form |
| 33 | | Update Classification with New Identifiers | 1. Admin → Edit Classification; 2. Add new identifiers; 3. Click Update Classification; 4. Verify | Identifiers: ["orange", "pink"]; Expected: New identifiers added to database |
| 34 | | Update Classification with Modified Identifiers | 1. Admin → Edit Classification; 2. Modify existing identifiers; 3. Click Update Classification; 4. Verify | Expected: Modified identifiers updated in database |
| 35 | | Update Classification with Removed Identifiers | 1. Admin → Edit Classification; 2. Remove identifiers; 3. Click Update Classification; 4. Verify | Expected: Removed identifiers soft deleted from database |
| 36 | | Success Message Display | 1. Admin → Any operation; 2. Complete successfully; 3. Check message | Expected: Success message displayed and auto-hides after 3 seconds |
| 37 | | Error Message Display | 1. Admin → Any operation; 2. Cause error; 3. Check message | Expected: Error message displayed and auto-hides after 5 seconds |
| 38 | | Manual Close Message | 1. Admin → Any operation; 2. Complete operation; 3. Click X on message; 4. Verify | Expected: Message closed immediately |
| 39 | | Responsive Design (Mobile) | 1. Access on mobile device; 2. Test all buttons and forms; 3. Verify layout | Expected: All elements responsive and functional on mobile |
| 40 | | Responsive Design (Tablet) | 1. Access on tablet device; 2. Test all buttons and forms; 3. Verify layout | Expected: All elements responsive and functional on tablet |
| 41 | | Input Field Width Consistency | 1. Admin → Edit Classification; 2. Compare input field widths; 3. Verify | Expected: All input fields have same width regardless of buttons |
| 42 | | Button Layout Consistency | 1. Admin → Edit Classification; 2. Check button positioning; 3. Verify | Expected: Buttons positioned beside inputs, not below |
| 43 | | Form Validation (Real-time) | 1. Admin → Any form; 2. Enter invalid data; 3. Check validation | Expected: Real-time validation errors displayed |
| 44 | | Form Validation (Submit) | 1. Admin → Any form; 2. Submit with invalid data; 3. Check validation | Expected: Form submission blocked with validation errors |
| 45 | | Database Integrity (Soft Delete) | 1. Admin → Delete any item; 2. Check database; 3. Verify | Expected: Item soft deleted (deleted_at set), not permanently removed |
| 46 | | Database Integrity (Restore) | 1. Admin → Add item with same name as soft deleted; 2. Check database; 3. Verify | Expected: Soft deleted item restored, not duplicated |
| 47 | | Database Integrity (Cascade Delete) | 1. Admin → Delete ride type; 2. Check related data; 3. Verify | Expected: All related classifications and identifiers soft deleted |
| 48 | | Database Integrity (Cascade Restore) | 1. Admin → Restore ride type; 2. Check related data; 3. Verify | Expected: All related data restored or recreated |
| 49 | | Session Management | 1. Admin → Perform operations; 2. Check session persistence; 3. Verify | Expected: All operations maintain session state |
| 50 | | CSRF Protection | 1. Admin → Submit form without CSRF token; 2. Check response; 3. Verify | Expected: Error - CSRF token mismatch |
| 51 | | XSS Prevention | 1. Admin → Enter script in text field; 2. Save; 3. Check output | Input: <script>alert('XSS')</script>; Expected: Script sanitized, displayed as text |
| 52 | | SQL Injection Prevention | 1. Admin → Enter SQL injection in text field; 2. Save; 3. Check database | Input: ' OR '1'='1; Expected: Input treated as literal text, no SQL execution |
| 53 | | Unauthorized Access | 1. Access admin routes without login; 2. Check response; 3. Verify | Expected: Redirected to login page |
| 54 | | Role-based Access | 1. Login as non-admin; 2. Try to access admin routes; 3. Check response | Expected: Access denied or redirected |
| 55 | | Data Persistence | 1. Admin → Add data; 2. Refresh page; 3. Check data | Expected: Data persists after page refresh |
| 56 | | Concurrent Operations | 1. Two admins edit same item; 2. Save changes; 3. Check result | Expected: Last save wins, no data corruption |
| 57 | | Large Dataset Handling | 1. Admin → Add many ride types/classifications; 2. Test performance; 3. Verify | Expected: System handles large datasets without performance issues |
| 58 | | Error Recovery | 1. Admin → Cause system error; 2. Check error handling; 3. Verify | Expected: Graceful error handling, no system crash |
| 59 | | Data Validation (Boundary) | 1. Admin → Enter maximum allowed values; 2. Save; 3. Verify | Expected: System handles boundary values correctly |
| 60 | | Data Validation (Special Characters) | 1. Admin → Enter special characters; 2. Save; 3. Verify | Expected: Special characters handled correctly |
| 61 | | Performance (Page Load) | 1. Admin → Load rides rate page; 2. Measure load time; 3. Verify | Expected: Page loads within acceptable time |
| 62 | | Performance (Database Queries) | 1. Admin → Perform operations; 2. Monitor database queries; 3. Verify | Expected: Efficient database queries, no N+1 problems |
| 63 | | Memory Usage | 1. Admin → Perform multiple operations; 2. Monitor memory; 3. Verify | Expected: Memory usage remains stable |
| 64 | | Browser Compatibility (Chrome) | 1. Test in Chrome; 2. Verify all functionality; 3. Check | Expected: All features work in Chrome |
| 65 | | Browser Compatibility (Firefox) | 1. Test in Firefox; 2. Verify all functionality; 3. Check | Expected: All features work in Firefox |
| 66 | | Browser Compatibility (Safari) | 1. Test in Safari; 2. Verify all functionality; 3. Check | Expected: All features work in Safari |
| 67 | | Browser Compatibility (Edge) | 1. Test in Edge; 2. Verify all functionality; 3. Check | Expected: All features work in Edge |
| 68 | | Accessibility (Screen Reader) | 1. Use screen reader; 2. Navigate interface; 3. Verify | Expected: Interface accessible via screen reader |
| 69 | | Accessibility (Keyboard Navigation) | 1. Use keyboard only; 2. Navigate interface; 3. Verify | Expected: All functionality accessible via keyboard |
| 70 | | Accessibility (Color Contrast) | 1. Check color contrast; 2. Verify readability; 3. Check | Expected: Sufficient color contrast for readability |
| 71 | | Internationalization | 1. Test with different languages; 2. Verify text display; 3. Check | Expected: Text displays correctly in different languages |
| 72 | | Localization (Date Format) | 1. Check date formats; 2. Verify consistency; 3. Check | Expected: Date formats consistent with locale |
| 73 | | Localization (Number Format) | 1. Check number formats; 2. Verify consistency; 3. Check | Expected: Number formats consistent with locale |
| 74 | | Backup and Recovery | 1. Admin → Perform operations; 2. Simulate data loss; 3. Restore; 4. Verify | Expected: Data can be restored from backup |
| 75 | | Data Export | 1. Admin → Export data; 2. Verify format; 3. Check completeness | Expected: Data exported in correct format with all fields |
| 76 | | Data Import | 1. Admin → Import data; 2. Verify import; 3. Check data integrity | Expected: Data imported correctly with validation |
| 77 | | Audit Trail | 1. Admin → Perform operations; 2. Check audit logs; 3. Verify | Expected: All operations logged with timestamps and user info |
| 78 | | Data Retention | 1. Admin → Check old data; 2. Verify retention policy; 3. Check | Expected: Data retained according to policy |
| 79 | | Data Archival | 1. Admin → Archive old data; 2. Verify archival; 3. Check | Expected: Old data archived correctly |
| 80 | | Data Purging | 1. Admin → Purge old data; 2. Verify purging; 3. Check | Expected: Old data purged according to policy |
| 81 | | System Monitoring | 1. Admin → Monitor system; 2. Check metrics; 3. Verify | Expected: System metrics available and accurate |
| 82 | | Log Management | 1. Admin → Check logs; 2. Verify log rotation; 3. Check | Expected: Logs rotated and managed correctly |
| 83 | | Security Scanning | 1. Admin → Run security scan; 2. Check vulnerabilities; 3. Verify | Expected: No critical vulnerabilities found |
| 84 | | Penetration Testing | 1. Admin → Run penetration test; 2. Check security; 3. Verify | Expected: System passes penetration tests |
| 85 | | Load Testing | 1. Admin → Run load test; 2. Check performance; 3. Verify | Expected: System handles expected load |
| 86 | | Stress Testing | 1. Admin → Run stress test; 2. Check system behavior; 3. Verify | Expected: System handles stress gracefully |
| 87 | | Volume Testing | 1. Admin → Test with large volumes; 2. Check performance; 3. Verify | Expected: System handles large volumes |
| 88 | | Spike Testing | 1. Admin → Test with traffic spikes; 2. Check behavior; 3. Verify | Expected: System handles traffic spikes |
| 89 | | Endurance Testing | 1. Admin → Run long-term test; 2. Check stability; 3. Verify | Expected: System remains stable over time |
| 90 | | Scalability Testing | 1. Admin → Test scalability; 2. Check performance; 3. Verify | Expected: System scales with increased load |
| 91 | | Compatibility Testing | 1. Admin → Test compatibility; 2. Check integration; 3. Verify | Expected: System compatible with all components |
| 92 | | Regression Testing | 1. Admin → Run regression tests; 2. Check functionality; 3. Verify | Expected: All existing functionality works |
| 93 | | Smoke Testing | 1. Admin → Run smoke tests; 2. Check basic functionality; 3. Verify | Expected: Basic functionality works |
| 94 | | Sanity Testing | 1. Admin → Run sanity tests; 2. Check critical paths; 3. Verify | Expected: Critical paths work correctly |
| 95 | | User Acceptance Testing | 1. Admin → Run UAT; 2. Check user requirements; 3. Verify | Expected: System meets user requirements |
| 96 | | Integration Testing | 1. Admin → Test integrations; 2. Check connections; 3. Verify | Expected: All integrations work correctly |
| 97 | | System Testing | 1. Admin → Test entire system; 2. Check end-to-end; 3. Verify | Expected: Entire system works correctly |
| 98 | | Acceptance Testing | 1. Admin → Run acceptance tests; 2. Check acceptance criteria; 3. Verify | Expected: System meets acceptance criteria |
| 99 | | Production Testing | 1. Admin → Test in production; 2. Check live system; 3. Verify | Expected: System works in production environment |
| 100 | | Maintenance Testing | 1. Admin → Test maintenance procedures; 2. Check maintenance; 3. Verify | Expected: Maintenance procedures work correctly |
