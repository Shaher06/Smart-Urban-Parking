# Login – Black-Box Test Cases

| TC# | Input | Expected Output | Pass/Fail |
|-----|-------|-----------------|-----------|
| TC01 | Valid email + correct password (driver) | Redirect to driver dashboard | |
| TC02 | Valid email + correct password (admin) | Redirect to admin dashboard | |
| TC03 | Valid email + wrong password | Error: Invalid credentials | |
| TC04 | Non-existent email | Error: Invalid credentials | |
| TC05 | Empty email | Validation error: email required | |
| TC06 | Empty password | Validation error: password required | |
| TC07 | Suspended account credentials | Error: account suspended | |
| TC08 | SQL injection in email field (`' OR 1=1--`) | Login fails safely | |
| TC09 | XSS in email field (`<script>alert(1)</script>`) | Escaped, no execution | |
| TC10 | Already logged in user visits login | Redirects to dashboard | |