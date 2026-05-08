# Booking – Black-Box Test Cases

| TC# | Scenario | Expected | Pass/Fail |
|-----|----------|----------|-----------|
| TC01 | Book available spot with valid times | Booking confirmed, QR generated | |
| TC02 | Book spot with start > end | Error: end must be after start | |
| TC03 | Book fully occupied spot | Error: no available slots | |
| TC04 | Book spot with overlapping time | Error: conflict detected | |
| TC05 | Book with buffer time violated | Error: buffer time message | |
| TC06 | Apply valid promo code | Discount applied to total | |
| TC07 | Apply invalid promo code | No discount, booking proceeds | |
| TC08 | Cancel reservation >2hr before start | 100% refund | |
| TC09 | Cancel reservation 1-2hr before start | 50% refund | |
| TC10 | Cancel reservation <1hr before start | 0% refund | |
| TC11 | Extend reservation (slot available) | Extended successfully | |
| TC12 | Extend reservation (slot conflict) | Error: cannot extend | |
| TC13 | QR check-in on confirmed reservation | Status → active | |
| TC14 | QR check-out on active reservation | Status → completed | |