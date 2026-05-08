# Payment – Black-Box Test Cases

| TC# | Scenario | Expected | Pass/Fail |
|-----|----------|----------|-----------|
| TC01 | Pay reservation with credit card | Payment completed, receipt generated | |
| TC02 | Pay fine with debit card | Fine marked paid | |
| TC03 | Pay with valid promo code | Discounted amount charged | |
| TC04 | View payment history | List of all payments shown | |
| TC05 | View receipt for existing payment | Receipt displayed with ref | |
| TC06 | View receipt for non-existent payment | Error or empty | |
| TC07 | Check escrow status for locked payment | Shows locked flag | |
| TC08 | Validate promo code WELCOME10 | 10% discount confirmed | |
| TC09 | Validate expired promo code | Error: invalid or expired | |
| TC10 | Request payout as owner | Payout created with net amount | |