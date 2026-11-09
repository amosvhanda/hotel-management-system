# Mopane Heights HMS — Premium Features Implementation Plan

Status: **draft for approval** · Nothing below is built yet.
Scope: the gaps found in the premium review (guest communication, pricing & revenue, front desk, guest experience, SEO, reports & trust), planned against the code as it is today.

---

## 1. How we will build it

**Principles**

- **Every phase ships on its own.** Each phase is one or more pushes that go live through the existing pipeline (tests → deploy → smoke test → 60-second re-check). Nothing waits for the whole plan.
- **No breaking changes.** Migrations only add tables/columns. Today's booking flow (search → book → Paynow/EcoCash/cash → invoice → cancel → review) keeps working at every step.
- **Switches in Admin, not code.** New behaviour that changes money or messages (deposits, reminders, WhatsApp, promo codes, seasonal rates, currency display) gets an on/off switch in Admin → Settings, off by default until you turn it on.
- **Tests with every feature.** Each feature adds feature tests (current suite: 128). The deploy smoke test grows with each new page.
- **One price engine.** Today the price is worked out in three places (checkout controller, admin edit, page JavaScript). Phase 3 replaces this with a single `PriceCalculator` that everything uses, so the page, checkout, invoice and reports always agree.

**Things in the code today that the plan depends on**

| Today | Why it matters | Where it's handled |
|---|---|---|
| `QUEUE_CONNECTION=sync` — emails send during the guest's request | Slow pages; a mail failure can break checkout | Phase 0: queue worker |
| Scheduler runs but has **no tasks** | No reminders or automatic jobs possible | Phase 0/1: scheduled commands |
| `config/app.php` timezone is **UTC** | Reminders "at 9am" would be 11am Harare | Phase 0: `Africa/Harare` |
| Booking `status` = 0 pending / 1 confirmed / 2 cancelled | No checked-in, checked-out, no-show | Phase 2 |
| `payment_status` is just 0/1, one `total_price` | No deposits, part payments or refunds | Phase 3: payments ledger |
| `rooms.price` is a **string**, one price + one % discount | No seasonal/weekend pricing | Phase 3: rate rules |
| Services have title/description/icon only | Can't sell extras | Phase 3: bookable extras |
| Only one email exists (`BookConfirm`) | Guests hear nothing on book/pay/cancel | Phase 1 |
| Booking requires an account | Friction for first-time guests | Phase 4 |
| Two `create_services_table` migrations exist | Risk on fresh installs | Phase 0 clean-up |

---

## Progress

| Phase | Status |
|---|---|
| 0 · Foundations | ✅ Built + tested (queue worker, Harare time, message log, audit trail + booking history, status helper, failed-message page, duplicate migration fixed). Stripe removal deferred to the Laravel upgrade (needs a composer lock update). |
| 1 · Communication + SEO | ✅ Built + tested (8 editable emails incl. reminders + rate-your-stay, test-send, WhatsApp buttons, sitemap.xml, robots.txt, link previews, Hotel/HotelRoom data for Google, Search Console field). Automatic WhatsApp (Meta API) later. |
| 2 · Front desk | ⏳ Next |
| 3 · Pricing & revenue | — |
| 4 · Guest experience | — |
| 5 · Reports & trust | — |

## 2. Phases at a glance

| # | Phase | What guests / staff get | Effort* | Depends on |
|---|---|---|---|---|
| 0 | Foundations | Faster pages, reliable emails, Harare time, audit trail | 2 days | — |
| 1 | Guest communication + SEO quick wins | Emails at every step, reminders, WhatsApp, Google/WhatsApp link previews | 4–5 days | 0 |
| 2 | Front desk | Check-in/out, no-shows, availability calendar, housekeeping, phone/walk-in bookings | 4–5 days | 0 |
| 3 | Pricing & revenue | Seasonal/weekend rates, promo codes, deposits, extras, cancellation policy & refunds | 6–8 days | 0, 2 |
| 4 | Guest experience | Book without an account, manage-booking page with QR, date-change requests, ZiG/USD display | 3–4 days | 1, 3 |
| 5 | Reports & trust | Occupancy/ADR/RevPAR, Excel export, contact inbox, staff 2-step login, backups | 4–5 days | 2, 3 |

\*Working days of development, including tests. About **5–6 weeks** in total. Phases 1 and 2 can run in either order.

---

## 3. Phase details

### Phase 0 — Foundations (2 days)

| Item | What we do |
|---|---|
| Queue worker | New `queue` service in `docker-compose.yml` (same image, runs `php artisan queue:work` as www-data, restarts on deploy). `QUEUE_CONNECTION=database` + `jobs`/`failed_jobs` tables. All emails/WhatsApp go through the queue. Failed jobs show in Admin. |
| Timezone | `APP_TIMEZONE=Africa/Harare`; dates stored as before. |
| Message log | `message_log` table: booking, channel (email/WhatsApp), template, recipient, status, error, sent_at. Lets staff see "was the reminder sent?" on the booking page, and stops anything being sent twice. |
| Audit trail | `spatie/laravel-activitylog` on Booking, Payment, Review, SiteSetting, Room: who changed what and when. Shown as a timeline on the booking page. |
| Booking status helper | Move status names/colours into the `Booking` model (one place) instead of repeated `@if status === 1` in 5 views. Required before Phase 2 adds statuses. |
| Clean-up | Remove the duplicate `create_services_table` migration safely; remove unused `stripe/stripe-php`. |

Deploy notes: new container + migrations; smoke test checks the queue worker is running.

---

### Phase 1 — Guest communication + SEO quick wins (4–5 days)

**1a. Emails (all queued, branded with logo and colours, plain-text version included)**

| Email | When | To |
|---|---|---|
| Booking received | Right after checkout (cash) | Guest |
| Payment received | Paynow/EcoCash payment clears | Guest |
| Booking confirmed | Staff confirm (exists — restyle + queue) | Guest |
| Booking cancelled | Guest or staff cancels / payment fails | Guest |
| Pre-arrival reminder | 2 days before check-in, 09:00 Harare | Guest |
| Check-in day welcome | Morning of arrival: directions, check-in time, WhatsApp/phone | Guest |
| Rate your stay | Day after check-out → links to the review form | Guest |
| New booking / payment | As they happen (bell already exists) | Staff (optional email copy) |

- Scheduled command `bookings:send-reminders` runs every 15 min; each email is sent once (tracked in `message_log`).
- Admin → Settings → **Guest messages**: switch each message on/off, edit the wording with placeholders (`{guest}`, `{room}`, `{check_in}`, `{code}`, `{total}`), send a test to yourself.
- The "Rate your stay" email feeds the review system built last week.

**1b. WhatsApp**

- Step 1 (no cost, no approval needed): "Chat on WhatsApp" button on the site + "Message guest on WhatsApp" button on each booking in Admin (opens WhatsApp with the booking details filled in).
- Step 2 (automatic messages): booking confirmation and pre-arrival reminder by WhatsApp through a provider. **Needs your decision** (see §5) — Meta WhatsApp Cloud API needs a verified Meta Business account and approved message templates.
- Guests choose at checkout: "Send updates by WhatsApp".

**1c. SEO & sharing (quick wins before you advertise)**

- `sitemap.xml` (home, rooms, room pages, services, blog posts, gallery, contact) + `robots.txt`.
- Link previews on WhatsApp/Facebook/X: title, description and photo per page (room photo on room pages, post image on blog).
- Google hotel data (schema.org `Hotel`, `HotelRoom`, `Offer` with price, `AggregateRating` from approved reviews) so Google can show stars and prices.
- Canonical URLs; Search Console verification code field in Admin → Settings.

Tests: each email sends once at the right time (time-travel tests), switches respected, sitemap lists live rooms/posts, page meta tags present.

---

### Phase 2 — Front desk (4–5 days)

**2a. Stay status**

New statuses: `3 checked-in`, `4 checked-out`, `5 no-show` (existing 0/1/2 unchanged).

| Action | Rules | Effect |
|---|---|---|
| Check in | From check-in day; booking confirmed; rooms assigned (prompts to assign if not) | Room numbers marked occupied; time + staff recorded |
| Check out | Checked-in bookings | Rooms → "needs cleaning"; balance-due warning if unpaid (Phase 3); triggers review request (Phase 1) |
| No-show | After check-in day if not checked in; manual, or automatic at a set hour (setting) | Remaining nights released; deposit kept per policy (Phase 3) |
| Early departure | Checked-in guest leaves early | Unused nights released |

Dashboard: "Arrivals today / In house / Departures today" become actionable lists with Check in / Check out buttons. Guests see "Checked in" / "Completed" in My Bookings.

**2b. Availability calendar (Admin)**

- Grid: room numbers × next 14/30 days, coloured by booking status; click a block to open the booking; drag-free (read-only) in v1.
- Filters by room type; today highlighted; mobile-friendly horizontal scroll.

**2c. Housekeeping**

- `room_numbers` gets `housekeeping_status`: clean / needs cleaning / in progress / out of order (+ note).
- Housekeeping board (works on a phone) for a new "Housekeeping" role: tap to mark clean.
- Out-of-order rooms are excluded from availability automatically.

**2d. Phone & walk-in bookings**

- Rebuild "Add Booking": pick dates/room → live availability and price → guest name/phone/email (no account needed) → payment method → optional "check in now" for walk-ins.
- Guest gets the same emails as an online booking.

Tests: status transitions allowed/blocked, availability frees correctly on no-show/early departure, out-of-order rooms not sellable, calendar renders.

---

### Phase 3 — Pricing & revenue (6–8 days)

**3a. Price engine** — `App\Services\PriceCalculator`

Input: room, dates, rooms, guests, extras, promo code. Output: nightly breakdown, extras, discounts, taxes (optional), total, deposit due, balance. Used by the booking sheet (via a JSON endpoint), checkout, admin booking, invoice and reports. The nightly breakdown is saved on the booking (`booking_nights` table) so later rate changes never change an existing booking.

**3b. Rate rules (seasonal / weekend / long stay)**

| Table `rate_rules` | |
|---|---|
| room (or all rooms), name, date range, weekdays (e.g. Fri–Sat), adjust type (% / fixed / set price), value, min nights, priority, active | |

Examples: "Festive season +20% 20 Dec–5 Jan", "Weekend +$15 Fri–Sat", "7+ nights −10%". The booking sheet shows "from $X/night" and the nightly breakdown. Admin screen with a 12-month preview of prices per room.

**3c. Promo codes**

`promo_codes`: code, % or fixed, valid from/to, min nights, rooms allowed, max uses (total and per guest), used count, active. Field at checkout with instant check; shown on invoice; usage report.

**3d. Payments ledger, deposits & balances**

- New `payments` table: booking, type (deposit / balance / full / refund / adjustment), method (Paynow, EcoCash, cash, card at desk, bank), amount, reference, status, staff, date.
- Booking gets `amount_paid` and `balance_due` (kept in sync from payments). `payment_status` becomes unpaid / part-paid / paid (old 0/1 values mapped).
- Setting: **deposit** none / % / first night, required for cash bookings or optional.
- Guests can pay a deposit online (Paynow/EcoCash) and the rest at the hotel; or pay the balance later from My Bookings.
- Front desk records cash/card payments in Admin; check-out warns about any balance.
- Paynow amount checks switch from `total_price` to "amount for this payment".
- Invoice shows payments made and balance due.

**3e. Extras at checkout**

- Services gain: bookable (yes/no), price, per stay / per night / per guest / per guest per night, photo.
- Checkout shows extras (airport transfer, breakfast, late check-out …) with quantities; included in total, invoice, emails.

**3f. Cancellation policy & refunds**

- Setting: free cancellation until N days before check-in; after that keep deposit / X% / first night.
- Policy shown on room page, checkout, emails and invoice.
- Guest "Cancel booking" shows the exact fee before confirming; paid bookings can now be cancelled online under the policy.
- Refunds recorded as `refund` payments by staff (Paynow has no automatic refund API — staff refund via EcoCash/bank and record it).

Data migration: existing bookings get one `booking_nights` row per night at their original price and one `payments` row if paid — totals unchanged (tested against a copy of production data).

Tests: price engine table-driven tests (seasons overlapping, weekends, long stay, promo + discount order, rounding), deposit flows, balance payment, refund maths, invoice totals = booking totals.

---

### Phase 4 — Guest experience (3–4 days)

| Item | What we do |
|---|---|
| Book without an account | Checkout asks for name, email, phone only. An account is created quietly and a "set your password" link is emailed. Existing-email guests are asked to log in. |
| Manage-booking page | Private link (in every email) + QR code: dates, room, payments, balance, pay now, cancel (with policy fee), add extras, download invoice. Works without logging in (signed link). QR shown at reception. |
| Change dates | Guest requests new dates → price difference shown → staff approve in Admin (v1: request; v2: instant if available). |
| Currency display | Base currency stays USD. Admin sets ZiG rate (and optionally ZAR); guests can switch display currency; checkout shows the amount charged in USD. |
| My Bookings polish | Upcoming / past / cancelled tabs, booking timeline, re-book same room. |

Tests: guest checkout creates account once, signed links expire/forge-proof, currency maths, date-change pricing.

---

### Phase 5 — Reports & trust (4–5 days)

| Item | What we do |
|---|---|
| Reports | Date range + room type filters: occupancy %, ADR (average daily rate), RevPAR, revenue by room type / payment method / channel (online, phone, walk-in), cancellations & no-shows, lead time, promo usage, extras sold. Charts + tables. |
| Exports | Excel/CSV for bookings, payments and reports (`maatwebsite/excel` is already installed). |
| Contact inbox | Read/unread, assign to staff, reply by email from Admin (logged), mark done. |
| Staff 2-step login | Email code (or authenticator app — see §5) for admin roles; remember device 30 days. |
| Security | Login throttling for staff, password strength, session timeout for Admin, audit log viewer (from Phase 0). |
| Backups | Nightly database backup at 02:00 Harare from the scheduler, 14 days kept on the server; optional off-site copy (S3/Backblaze/Google Drive); restore guide in `docs/`. Smoke test checks last backup is < 26 h old. |

---

## 4. Delivery & release plan

- **Order:** 0 → 1 → 2 → 3 → 4 → 5 (1 and 2 can swap).
- **Per phase:** build locally → full test suite + new tests → browser walk-through on a fresh production-like database → push → pipeline deploy + smoke test → I check the live site.
- **Demo content:** the demo seeder gets sample rate rules, promo code (`WELCOME10`), extras, and a few completed stays with reviews, so the live demo shows everything.
- **Docs:** each phase updates `README.md` and adds an admin "how to" page in `docs/`.
- **Rollback:** every migration is additive and reversible; switches let you turn a feature off without a redeploy.

## 5. Decisions needed from you

| # | Decision | Options | My suggestion |
|---|---|---|---|
| 1 | Automatic WhatsApp messages | (a) click-to-WhatsApp links only, (b) Meta WhatsApp Cloud API (free tier, needs Meta Business verification + templates), (c) Twilio (paid per message, quicker setup) | Start (a) in Phase 1, add (b) when your Meta Business account is verified |
| 2 | Deposit for cash bookings | none / % / first night; required or optional | Optional 30% deposit, required in festive season |
| 3 | Cancellation policy | free until N days, then fee | Free until 3 days before; then first night |
| 4 | Display currencies | USD only / USD + ZiG / + ZAR | USD + ZiG (rate set by staff) |
| 5 | Taxes / levies on invoice | none / VAT / tourism levy % | Confirm what applies to the hotel |
| 6 | Staff 2-step login | email code / authenticator app | Email code (easier for staff) |
| 7 | Off-site backups | server only / S3 / Backblaze / Google Drive | Backblaze B2 (cheap) |
| 8 | Guest checkout without account | yes / no | Yes |
| 9 | Laravel 11 upgrade | now / after Phase 5 | After Phase 5 (separate, low risk) |

## 6. Risks

| Risk | Mitigation |
|---|---|
| Price engine changes totals on existing bookings | Totals frozen per booking (`booking_nights`); migration verified against production copy |
| Reminders sent twice or at the wrong hour | `message_log` uniqueness + Harare timezone + time-travel tests |
| WhatsApp provider approval delays | Phase 1 ships with click-to-WhatsApp; automation added later behind a switch |
| Emails land in spam | SPF/DKIM/DMARC for `hotel.synccode.co.zw` + proper SMTP provider (checklist in docs) |
| Bigger deploys | Phase-by-phase pushes, smoke test + 60-second re-check already in the pipeline |
| The current production 500 | Must be resolved first (the self-repair deploy is waiting to be pushed) |
