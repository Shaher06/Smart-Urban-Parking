# Fine – Black-Box Test Cases

| TC# | Scenario | Expected | Pass/Fail |
|-----|----------|----------|-----------|
| TC01 | Admin issues fine to driver | Fine created, notification sent | |
| TC02 | Driver views unpaid fines | Fine list displayed | |
| TC03 | Driver pays unpaid fine | Status → paid | |
| TC04 | Driver pays already-paid fine | Error: already paid | |
| TC05 | Driver submits appeal with reason | Appeal created, fine → appealed | |
| TC06 | Driver submits duplicate appeal | Error: already pending | |
| TC07 | Admin approves appeal | Fine → waived, notification sent | |
| TC08 | Admin rejects appeal | Fine → unpaid, notification sent | |
| TC09 | Driver accumulates 3+ unpaid fines | Account blacklisted | |
| TC10 | Officer issues fine from dispatch screen | Fine created with officer as issuer | |