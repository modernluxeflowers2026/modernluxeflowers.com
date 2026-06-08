<?php
// Session auth — shares config/session with dashboard
require_once __DIR__ . '/../dashboard/config.php';

ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_samesite', 'Strict');
if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') {
    ini_set('session.cookie_secure', 1);
}

session_start();

$authenticated = (
    isset($_SESSION['hphq_auth'], $_SESSION['hphq_expires']) &&
    $_SESSION['hphq_auth'] === true &&
    time() < $_SESSION['hphq_expires']
);

if (!$authenticated) {
    session_destroy();
    header('Location: /dashboard/login.php');
    exit;
}

$_SESSION['hphq_expires'] = time() + SESSION_LIFETIME;
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Agent Zero &mdash; HPHQ</title>
  <script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>
  <style>
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

    html, body {
      height: 100%;
      font-family: 'Inter', 'Helvetica Neue', Arial, sans-serif;
    }

    body {
      min-height: 100vh;
      background: #c8b89a url('bg.png') center center / cover no-repeat fixed;
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: flex-start;
      padding: 28px 20px 40px;
    }

    /* ── Header ── */
    .az-header {
      width: 100%;
      max-width: 1160px;
      display: flex;
      align-items: baseline;
      gap: 14px;
      margin-bottom: 22px;
    }
    .az-wordmark {
      font-size: 1.15rem;
      font-weight: 700;
      letter-spacing: 0.04em;
      color: #1a1a1a;
    }
    .az-subline {
      font-size: 0.72rem;
      letter-spacing: 0.14em;
      text-transform: uppercase;
      color: #6b6458;
    }
    .az-logout {
      margin-left: auto;
      font-size: 0.72rem;
      letter-spacing: 0.1em;
      text-transform: uppercase;
      color: #888;
      text-decoration: none;
      transition: color 0.15s;
    }
    .az-logout:hover { color: #C0392B; }

    /* ── Two-column main layout ── */
    .az-main {
      width: 100%;
      max-width: 1160px;
      display: grid;
      grid-template-columns: 340px 1fr;
      gap: 18px;
      align-items: start;
    }

    /* ── Frosted panel base ── */
    .panel {
      background: rgba(255,255,255,0.88);
      backdrop-filter: blur(14px);
      -webkit-backdrop-filter: blur(14px);
      border: 1px solid rgba(255,255,255,0.95);
      border-radius: 12px;
      box-shadow: 0 4px 24px rgba(0,0,0,0.10);
      padding: 24px;
    }

    /* ── Left: inputs ── */
    .input-panel {}
    .field-label {
      font-size: 9.5px;
      font-weight: 700;
      letter-spacing: 0.18em;
      text-transform: uppercase;
      color: #6b6458;
      margin-bottom: 7px;
      display: block;
    }
    textarea {
      width: 100%;
      background: rgba(255,255,255,0.9);
      border: 1.5px solid #ddd;
      border-radius: 7px;
      padding: 12px 14px;
      font-size: 13.5px;
      font-family: inherit;
      color: #1a1a1a;
      resize: vertical;
      outline: none;
      transition: border-color 0.15s;
      line-height: 1.6;
    }
    textarea:focus { border-color: #C0392B; }
    #az-goal { min-height: 130px; margin-bottom: 18px; }
    #az-tools { min-height: 110px; margin-bottom: 20px; }

    .run-btn {
      width: 100%;
      background: #C0392B;
      color: #fff;
      border: none;
      border-radius: 7px;
      padding: 12px 0;
      font-size: 13px;
      font-weight: 700;
      font-family: inherit;
      letter-spacing: 0.05em;
      cursor: pointer;
      transition: background 0.15s;
    }
    .run-btn:hover { background: #a93226; }
    .run-btn:disabled { background: #ccc; cursor: not-allowed; }

    .hint {
      margin-top: 14px;
      font-size: 10.5px;
      color: #999;
      line-height: 1.6;
      text-align: center;
    }

    /* ── Right: output ── */
    .output-panel {
      min-height: 480px;
      display: flex;
      flex-direction: column;
    }
    .output-header {
      display: flex;
      align-items: center;
      justify-content: space-between;
      margin-bottom: 16px;
    }
    .output-label {
      font-size: 9.5px;
      font-weight: 700;
      letter-spacing: 0.18em;
      text-transform: uppercase;
      color: #6b6458;
    }
    .output-actions {
      display: flex;
      gap: 7px;
    }
    .action-btn {
      background: transparent;
      border: 1.5px solid #ddd;
      border-radius: 5px;
      padding: 5px 13px;
      font-size: 11px;
      font-weight: 600;
      font-family: inherit;
      color: #555;
      cursor: pointer;
      transition: all 0.15s;
    }
    .action-btn:hover { border-color: #C0392B; color: #C0392B; }

    .output-body {
      flex: 1;
      min-height: 400px;
      font-size: 14px;
      color: #1a1a1a;
      line-height: 1.8;
      overflow-y: auto;
    }
    .output-placeholder {
      color: #aaa;
      font-style: italic;
      font-size: 13px;
      padding-top: 10px;
    }

    /* Markdown output styling */
    .output-body h1, .output-body h2 { font-size: 15px; font-weight: 700; color: #1a1a1a; margin: 18px 0 8px; border-bottom: 1px solid #eee; padding-bottom: 4px; }
    .output-body h3 { font-size: 13.5px; font-weight: 700; color: #1a1a1a; margin: 14px 0 6px; }
    .output-body p { margin-bottom: 10px; }
    .output-body ul, .output-body ol { padding-left: 20px; margin-bottom: 10px; }
    .output-body li { margin-bottom: 4px; }
    .output-body strong { font-weight: 700; }
    .output-body code { background: #f4f1ed; border-radius: 3px; padding: 1px 5px; font-size: 12.5px; font-family: 'Courier New', monospace; }
    .output-body pre { background: #f4f1ed; border-radius: 6px; padding: 14px; margin: 10px 0; overflow-x: auto; }
    .output-body pre code { background: none; padding: 0; }
    .output-body table { width: 100%; border-collapse: collapse; margin: 12px 0; font-size: 13px; }
    .output-body th { background: #f4f1ed; font-weight: 700; padding: 8px 12px; text-align: left; border: 1px solid #ddd; }
    .output-body td { padding: 7px 12px; border: 1px solid #e8e4de; vertical-align: top; }
    .output-body tr:nth-child(even) td { background: #faf8f4; }
    .output-body blockquote { border-left: 3px solid #C0392B; padding-left: 14px; color: #666; margin: 10px 0; }

    /* ── Loading ── */
    .az-loading {
      display: inline-flex;
      align-items: center;
      gap: 8px;
      color: #777;
      font-size: 13px;
      font-style: italic;
    }
    .az-loading::before {
      content: '';
      width: 14px;
      height: 14px;
      border: 2px solid #ddd;
      border-top-color: #C0392B;
      border-radius: 50%;
      animation: spin 0.7s linear infinite;
    }
    @keyframes spin { to { transform: rotate(360deg); } }

    /* ── Responsive ── */
    @media (max-width: 900px) {
      .az-main { grid-template-columns: 1fr; }
      .week-strip { grid-template-columns: 1fr 1fr; }
      .week-cell .wc-arrow { display: none; }
    }
    @media (max-width: 480px) {
      body { padding: 16px 12px 32px; }
      .week-strip { grid-template-columns: 1fr 1fr; }
      .panel { padding: 18px; }
    }
  </style>
</head>
<body>

  <!-- Header -->
  <div class="az-header">
    <span class="az-wordmark">Agent Zero</span>
    <span class="az-subline">Automation planner &mdash; Internal use only</span>
    <a class="az-logout" href="/dashboard/logout.php">Log out</a>
  </div>

  <!-- Two-column main -->
  <div class="az-main">

    <!-- Left: inputs -->
    <div class="panel input-panel">
      <label class="field-label" for="az-goal">What do you want to automate?</label>
      <textarea id="az-goal" placeholder="Describe any goal — business, content, personal productivity, operations. No limits."></textarea>

      <label class="field-label" for="az-tools">What tools do you have?</label>
      <textarea id="az-tools" placeholder="List everything available — apps, platforms, software, subscriptions (e.g. MailerLite, Canva, Gumroad, Claude, Zapier, Google Sheets)"></textarea>

      <button class="run-btn" id="run-btn" onclick="runAgentZero()">&#9654;&nbsp; Run Agent Zero</button>
      <p class="hint">Takes 20&ndash;40 seconds. Output renders in the panel on the right. Copy and paste into any dashboard agent when ready.</p>
    </div>

    <!-- Right: output -->
    <div class="panel output-panel">
      <div class="output-header">
        <span class="output-label">Automation Plan</span>
        <div class="output-actions">
          <button class="action-btn" id="copy-btn" onclick="copyOutput()">Copy</button>
          <button class="action-btn" id="dl-btn" onclick="downloadTXT()">&#8595; TXT</button>
          <button class="action-btn" onclick="clearAll()">Clear</button>
        </div>
      </div>
      <div class="output-body" id="az-output">
        <div class="output-placeholder">Enter a goal and tools, then run Agent Zero.</div>
      </div>
    </div>

  </div>

<script>
const PROXY = 'https://hphq-proxy.erik-a52.workers.dev/';
const MODEL = 'claude-sonnet-4-6';

const AGENT_ZERO_SYSTEM = `You are Agent Zero - a universal automation planner.

Your job is to take any goal and any set of available tools and produce a complete, actionable automation plan.

You will receive:
- A goal (anything - business, creative, personal productivity, content, operations)
- A list of tools or resources available

You will output a structured automation plan containing exactly these 7 sections in this order:

## 1. WORKFLOW
The step-by-step process from start to finish, shown as a numbered sequence.

## 2. WHAT TO AUTOMATE
A table showing each step, whether it is automated or manual, and why.

## 3. TOOLS TO USE
Which specific tools handle which steps and exactly why each tool was chosen.

## 4. EXACT PROMPTS
If Claude is part of the workflow, provide the exact ready-to-use prompt for each relevant step. No placeholders left unfilled.

## 5. HOW IT SAVES TIME
Specific time saved per task or per week. Give real estimates, not vague claims.

## 6. HOW IT MAKES MONEY
Direct or indirect revenue impact. Be specific about which step drives income or protects margin. If the goal has no direct revenue link, state that explicitly and explain why.

## 7. EXAMPLE OUTPUT
A concrete example showing what the final result looks like when the full workflow runs once.

Rules:
- All 7 sections are required. Never skip a section.
- Be specific. No vague advice. No motivational filler.
- If a step cannot be automated with the tools provided, say so plainly and give the manual alternative with a time estimate.
- Write for someone who will execute this immediately.
- Output must be copy-paste ready. No placeholders left unfilled.
- Format output in clean markdown: use headers (##), tables, numbered lists, and code blocks where appropriate.`;

var rawOutput = '';

async function runAgentZero() {
  var goal = document.getElementById('az-goal').value.trim();
  var tools = document.getElementById('az-tools').value.trim();
  if (!goal) { alert('Enter a goal to continue.'); return; }

  var userMsg = 'GOAL: ' + goal + (tools ? '\n\nTOOLS AVAILABLE:\n' + tools : '');
  var outputEl = document.getElementById('az-output');
  var btn = document.getElementById('run-btn');

  btn.disabled = true;
  btn.textContent = 'Planning...';
  outputEl.innerHTML = '<span class="az-loading">Agent Zero thinking...</span>';
  rawOutput = '';

  try {
    var resp = await fetch(PROXY, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({
        model: MODEL,
        max_tokens: 4096,
        system: AGENT_ZERO_SYSTEM,
        messages: [{ role: 'user', content: userMsg }]
      })
    });
    if (!resp.ok) throw new Error('API error ' + resp.status);
    var data = await resp.json();
    rawOutput = (data.content && data.content[0] && data.content[0].text) ? data.content[0].text : '';
    if (!rawOutput) throw new Error('Empty response');
    outputEl.innerHTML = marked.parse(rawOutput);
  } catch(err) {
    outputEl.innerHTML = '<div style="color:#C0392B;font-size:13px;padding:10px 0;">Error: ' + err.message + '. Check your connection and try again.</div>';
  } finally {
    btn.disabled = false;
    btn.innerHTML = '&#9654;&nbsp; Run Agent Zero';
  }
}

function copyOutput() {
  if (!rawOutput) return;
  navigator.clipboard.writeText(rawOutput).then(function() {
    var btn = document.getElementById('copy-btn');
    btn.textContent = 'Copied!';
    setTimeout(function() { btn.textContent = 'Copy'; }, 2000);
  });
}

function downloadTXT() {
  if (!rawOutput) return;
  var blob = new Blob(['Agent Zero Automation Plan\n' + new Date().toLocaleDateString() + '\n\n' + rawOutput], { type: 'text/plain' });
  var url = URL.createObjectURL(blob);
  var a = document.createElement('a');
  a.href = url;
  a.download = 'agent-zero-plan.txt';
  a.click();
  URL.revokeObjectURL(url);
}

function clearAll() {
  document.getElementById('az-goal').value = '';
  document.getElementById('az-tools').value = '';
  document.getElementById('az-output').innerHTML = '<div class="output-placeholder">Enter a goal and tools, then run Agent Zero.</div>';
  rawOutput = '';
}
</script>
</body>
</html>
