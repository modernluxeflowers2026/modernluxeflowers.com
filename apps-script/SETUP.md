# Lead Capture Setup — Google Sheet + Apps Script

## Where things stand (as of Aug 14, 2026)

**Setup is complete and live.** Steps 1–4 below are done — the script is deployed
(Version 2) and the site points at it. All that's left is confirming it works.

Pick up here:

- [ ] **Submit the live form once through each button** (Send Inquiry, then Send via
      WhatsApp) at modernluxeflowers.com/contact-us-floral-arrangements.html. See
      *Step 5 — Verify end to end* below for what each should produce.
- [ ] Delete the `Test Lead` row from the **MLF Contact Form Leads** Sheet.
- [ ] Rename the Apps Script project from **Untitled project** to `MLF Lead Capture`
      (cosmetic — makes it findable in Drive later).

One loose end: opening the `/exec` URL in a browser once returned Google's
"Sorry, unable to open the file at this time". That was before the Version 2
redeploy and may already be fixed. If the form test produces no Sheet rows, that
error is the cause — see *Troubleshooting* at the bottom for the fix (it's a
deployment access setting; the URL does not change).

A failed capture is silent by design: the form still works normally for visitors
and only the backup no-ops. So the form test is the only way to know.

---

One-time setup, ~10 minutes. After this, every contact form submission (both buttons)
lands in a Google Sheet and sends you an email, independently of Web3Forms.

Do all of this while signed into the Google account that should **own** the Sheet and
**send** the notification emails.

---

## Step 1 — Create the Apps Script project

You don't need to create the Sheet by hand — the script creates it on first run.
You also don't need to edit any code: the notification address is set through a
form in the next step.

> `apps-script/Code.gs` in this repo is the **source of record** — a reference copy
> kept alongside the site. The code that actually runs lives in your Apps Script
> project at script.google.com. Editing the repo copy changes nothing live, and
> editing the live copy changes nothing in the repo; if you change one, paste it
> across to the other so they don't drift.

1. Go to <https://script.google.com> → **New project**.
2. Rename it (top left) to `MLF Lead Capture`.
3. Delete the sample `function myFunction() {}` in `Code.gs`.
4. Open `apps-script/Code.gs` from this repo, copy the whole file, paste it in.
5. **Save** (⌘S / Ctrl+S).

## Step 1b — Notification address (already set)

Notifications go to **modernluxeflowers@gmail.com**, set as `NOTIFY_EMAIL_FALLBACK`
at the top of `Code.gs`. Nothing to do here — skip to Step 2.

**To send them somewhere else,** don't edit the code. In the Apps Script editor
sidebar → **Project Settings** (gear icon) → **Script Properties** →
**Add script property**:

| Property | Value |
|---|---|
| `NOTIFY_EMAIL` | the address you want instead |

**Save script properties.** The Script Property overrides the fallback, so this
survives re-pasting a fresh `Code.gs` over the top.

> This repo is **public**, so the fallback address is visible on GitHub (it is not
> served on the website — `_config.yml` keeps `apps-script/` out of the published
> site). To keep the address off GitHub entirely: set the Script Property above,
> then blank the `NOTIFY_EMAIL_FALLBACK` line back to `''` and commit.

## Step 2 — Authorize and create the Sheet

1. In the function dropdown at the top, select **`testLeadCapture`** → click **Run**.
2. Google will prompt for permissions: **Review permissions** → pick your account →
   **Advanced** → **Go to MLF Lead Capture (unsafe)** → **Allow**.
   (The "unsafe" warning is normal for a personal script Google hasn't verified.)
3. When it finishes, check:
   - **Google Drive** now has a spreadsheet named **MLF Contact Form Leads** with a
     bold header row and one `Test Lead` row.
   - Jo's inbox has an email titled `New Lead (Email form) — Test Lead — Wedding`.
4. Delete the test row from the Sheet.

If the run fails, open **Executions** in the left sidebar to see the error.

## Step 3 — Deploy as a Web App

1. Top right → **Deploy** → **New deployment**.
2. Click the gear next to "Select type" → **Web app**.
3. Fill in:
   - **Description:** `MLF lead capture v1`
   - **Execute as:** **Me** (your account)
   - **Who has access:** **Anyone** ← must be "Anyone", *not* "Anyone with Google account"
4. **Deploy** → authorize again if prompted → **copy the Web app URL**.
   It looks like `https://script.google.com/macros/s/AKfy...long.../exec`.
5. Sanity check: paste that URL into a browser tab. You should see
   `{"success":true,"status":"MLF lead capture is live"}`.

## Step 4 — Paste the URL into the site (done)

`LEAD_CAPTURE_URL` in `contact-us-floral-arrangements.html` already points at the
deployed endpoint. Nothing to do unless you redeploy to a **new** deployment (a new
*version* of the existing one keeps the same URL — see "Updating the script" below).

If you ever do need to change it, it's the one line in the `<script>` block near the
bottom of that file — search for `LEAD_CAPTURE_URL` — and it takes effect when the
change is pushed to `main`.

> Note: this URL is public in the page source. That's fine — the endpoint only ever
> *appends* rows, it can't read anything back out, and the worst case is junk rows.

## Step 5 — Verify end to end

On the live site, open `contact-us-floral-arrangements.html` with the browser console
open (F12 → Console).

**Test A — validation**

Leave name and email blank, click **Send Inquiry**. You should see a red
"Please enter your name and your email…" message and red borders on both fields, and
**no** network request. Repeat with **Send via WhatsApp** — same result, WhatsApp must
not open.

**Test B — email button**

Fill in name, email, and a couple of options. Click **Send Inquiry**. Expect all three:

| Where | What to look for |
|---|---|
| Sheet | New row, `Channel` column = `email` |
| Jo's inbox | Apps Script email, subject `New Lead (Email form) — …` |
| Web3Forms inbox | The existing Web3Forms notification |

Plus the on-page green "Thank you!" confirmation.

**Test C — WhatsApp button**

Refill the form, click **Send via WhatsApp**. Expect:

| Where | What to look for |
|---|---|
| Sheet | New row, `Channel` column = `whatsapp` |
| Jo's inbox | Apps Script email, subject `New Lead (WhatsApp) — …` |
| WhatsApp | The prefilled chat opens as before |

No Web3Forms email on this path — that's expected, WhatsApp never sent one.

**Test D — GA4 split**

In GA4 → **Reports → Realtime**, find the `generate_lead` event and check the `channel`
parameter reads `email` vs `whatsapp` for the two tests above. Custom dimension setup
(if you want `channel` in standard reports): **Admin → Custom definitions → Create
custom dimension**, event-scoped, parameter name `channel`. New dimensions only collect
data going forward, so do this before you care about the numbers.

---

## Updating the script later

If you edit `Code.gs`, the live Web App keeps running the *old* code until you
redeploy: **Deploy → Manage deployments →** pencil icon **→ Version: New version →
Deploy**. The URL stays the same, so nothing on the site needs to change.

## Troubleshooting

| Symptom | Cause / fix |
|---|---|
| Console: `Lead capture URL not configured` | Step 4 wasn't done — the placeholder is still in the HTML. |
| Console: `Lead capture failed` + CORS error | Deployment access isn't **Anyone**. Redeploy with the right setting. |
| Rows appear, no email | The `NOTIFY_EMAIL` Script Property is missing or misspelled (Step 1b), or the daily MailApp quota (100/day on consumer Gmail) is spent. Check **Executions** in the Apps Script editor — the error names which. |
| Email arrives, no row | Check **Executions** for a Drive/Sheets permission error; re-run `testLeadCapture` to reauthorize. |
| Nothing at all after redeploy | You created a *new* deployment with a new URL instead of a new *version*. Either paste the new URL into the HTML or redeploy as a version of the existing one. |
