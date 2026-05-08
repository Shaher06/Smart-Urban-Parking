# File Upload – Black-Box Test Cases

| TC# | Scenario | Expected | Pass/Fail |
|-----|----------|----------|-----------|
| TC01 | Upload JPG appeal evidence | File saved to /uploads/evidence/ | |
| TC02 | Upload PDF appeal evidence | File saved to /uploads/evidence/ | |
| TC03 | Upload unsupported file type (.exe) | Error: invalid file type | |
| TC04 | Upload owner document (PDF) | File saved to /uploads/owner_documents/ | |
| TC05 | Upload profile image (PNG) | Image saved, profile updated | |
| TC06 | Upload file exceeding size limit | Server/PHP returns error | |
| TC07 | Submit appeal without evidence | Appeal accepted (evidence optional) | |
| TC08 | Admin views submitted owner docs | Document list shown | |
| TC09 | Download uploaded evidence from admin | File accessible via link | |
| TC10 | Upload empty file | Error: no file selected | |