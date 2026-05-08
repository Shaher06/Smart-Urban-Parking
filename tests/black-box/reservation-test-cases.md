# Reservation – Black-Box Test Cases

| TC# | Scenario | Expected | Pass/Fail |
|-----|----------|----------|-----------|
| TC01 | Driver browses all spots | Active spots listed | |
| TC02 | Filter spots by city | Only matching spots shown | |
| TC03 | Filter by EV support | Only EV spots shown | |
| TC04 | Filter by max price | Only affordable spots shown | |
| TC05 | Search nearby spots by lat/lng | Nearby spots sorted by distance | |
| TC06 | Add spot to favorites | Favorite saved | |
| TC07 | Remove spot from favorites | Favorite removed | |
| TC08 | Join waitlist for full spot | Added to waitlist | |
| TC09 | Join waitlist twice for same spot | Error: already waiting | |
| TC10 | Leave waitlist | Status → expired | |
| TC11 | View reservation history | Completed/cancelled shown | |
| TC12 | Add review for spot | Review saved with rating | |
| TC13 | Add duplicate review | Error: already reviewed | |