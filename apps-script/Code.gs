/**
 * Modern LUXE Flowers - contact form lead capture.
 *
 * Deploy as a Web App ("Execute as: Me", "Who has access: Anyone"), then paste
 * the /exec URL into LEAD_CAPTURE_URL in contact-us-floral-arrangements.html.
 *
 * Every submission - email button or WhatsApp button - writes one row to the
 * "MLF Contact Form Leads" spreadsheet and sends a plain-text notification to
 * NOTIFY_EMAIL. That notification is independent of Web3Forms, so a lead has
 * to survive: Sheet row + Apps Script email (+ Web3Forms email on the email path).
 */

// === CONFIG ===============================================================
// Notification address. Preferred: set a Script Property named NOTIFY_EMAIL
// (Apps Script editor -> Project Settings -> Script Properties) - that needs no
// code edit and survives pasting a fresh copy of this file over the top.
// The fallback below is only used when that property is absent.
var NOTIFY_EMAIL_FALLBACK = 'modernluxeflowers@gmail.com';
var SPREADSHEET_NAME = 'MLF Contact Form Leads';
var SHEET_NAME       = 'Leads';
var HEADERS = ['Timestamp', 'Name', 'Email', 'Event Date', 'Event Type',
               'Budget', 'Notes', 'Channel', 'Page URL'];
// ============================================================

function doPost(e) {
  try {
    if (!e || !e.postData || !e.postData.contents) {
      return jsonOut({ success: false, error: 'Empty request body' });
    }

    var d = JSON.parse(e.postData.contents);

    // Basic validation - never write a row we could not act on.
    var name  = String(d.name  || '').trim();
    var email = String(d.email || '').trim();
    if (!name || !email) {
      return jsonOut({ success: false, error: 'Missing required field: name and email' });
    }

    var channel = String(d.channel || '').trim().toLowerCase();
    if (channel !== 'email' && channel !== 'whatsapp') channel = 'unknown';

    var row = {
      timestamp: d.timestamp ? String(d.timestamp) : new Date().toISOString(),
      name:      name,
      email:     email,
      eventDate: String(d.eventDate || 'Not specified').trim(),
      eventType: String(d.eventType || 'Not specified').trim(),
      budget:    String(d.budget    || 'Not specified').trim(),
      notes:     String(d.notes     || '').trim(),
      channel:   channel,
      pageUrl:   String(d.pageUrl   || '').trim()
    };

    appendLead(row);

    // Notification is best-effort: a mail failure must not lose the row we
    // already wrote, so it reports separately instead of failing the request.
    var mailed = true;
    try {
      notify(row);
    } catch (mailErr) {
      mailed = false;
      console.error('Notification email failed: ' + mailErr);
    }

    return jsonOut({ success: true, mailed: mailed });

  } catch (err) {
    console.error('doPost failed: ' + err);
    return jsonOut({ success: false, error: String(err) });
  }
}

/** Health check - visiting the /exec URL in a browser should show this. */
function doGet() {
  return jsonOut({ success: true, status: 'MLF lead capture is live' });
}

function appendLead(row) {
  var sheet = getSheet();
  sheet.appendRow([
    row.timestamp, row.name, row.email, row.eventDate, row.eventType,
    row.budget, row.notes, row.channel, row.pageUrl
  ]);
}

/** Finds (or creates) the spreadsheet and its header row. */
function getSheet() {
  var props = PropertiesService.getScriptProperties();
  var id = props.getProperty('SPREADSHEET_ID');
  var ss = null;

  if (id) {
    try { ss = SpreadsheetApp.openById(id); } catch (e) { ss = null; }
  }
  if (!ss) {
    var files = DriveApp.getFilesByName(SPREADSHEET_NAME);
    if (files.hasNext()) {
      ss = SpreadsheetApp.open(files.next());
    } else {
      ss = SpreadsheetApp.create(SPREADSHEET_NAME);
    }
    props.setProperty('SPREADSHEET_ID', ss.getId());
  }

  var sheet = ss.getSheetByName(SHEET_NAME) || ss.getSheets()[0];
  sheet.setName(SHEET_NAME);

  if (sheet.getLastRow() === 0) {
    sheet.appendRow(HEADERS);
    sheet.getRange(1, 1, 1, HEADERS.length).setFontWeight('bold');
    sheet.setFrozenRows(1);
  }
  return sheet;
}

/** Script Property first, hard-coded fallback second. */
function getNotifyEmail() {
  var fromProps = PropertiesService.getScriptProperties().getProperty('NOTIFY_EMAIL');
  return String(fromProps || NOTIFY_EMAIL_FALLBACK || '').trim();
}

function notify(row) {
  var to = getNotifyEmail();
  if (!to) {
    throw new Error('NOTIFY_EMAIL is not configured - add it as a Script Property ' +
                    '(Project Settings -> Script Properties) or set NOTIFY_EMAIL_FALLBACK.');
  }

  var label = row.channel === 'whatsapp' ? 'WhatsApp' : 'Email form';
  var body = [
    'New consultation request - ' + label,
    '',
    'Name:       ' + row.name,
    'Email:      ' + row.email,
    'Event date: ' + row.eventDate,
    'Event type: ' + row.eventType,
    'Budget:     ' + row.budget,
    '',
    'Notes:',
    row.notes || '(none provided)',
    '',
    '---',
    'Channel:    ' + row.channel,
    'Submitted:  ' + row.timestamp,
    'Page:       ' + row.pageUrl
  ].join('\n');

  MailApp.sendEmail({
    to: to,
    subject: 'New Lead (' + label + ') - ' + row.name + ' - ' + row.eventType,
    body: body,
    replyTo: row.email
  });
}

function jsonOut(obj) {
  return ContentService
    .createTextOutput(JSON.stringify(obj))
    .setMimeType(ContentService.MimeType.JSON);
}

/** Run this once from the editor to grant permissions and smoke-test the setup. */
function testLeadCapture() {
  var out = doPost({
    postData: {
      contents: JSON.stringify({
        timestamp: new Date().toISOString(),
        name: 'Test Lead',
        email: 'test@example.com',
        eventDate: 'Sept 2026',
        eventType: 'Wedding',
        budget: '$1,500 - $5,000',
        notes: 'This is a test row - safe to delete.',
        channel: 'email',
        pageUrl: 'https://modernluxeflowers.com/contact-us-floral-arrangements.html'
      })
    }
  });
  Logger.log(out.getContent());
}
