#!/usr/bin/env python3
"""MLF brand/content check.

Standalone version of the pre-commit banned-word check (see
.githooks/pre-commit). Runs the same rules against a single file, so it can
be wired into the Module 7 caption-approval step in Make.com before
anything posts.

Usage:
    brand_check.py FILE [FILE ...]
    brand_check.py --stdin --name NAME
    brand_check.py --json FILE

Exit code is 1 if any violation is found, 0 otherwise.
"""

import argparse
import json
import os
import re
import sys
from pathlib import Path

# Banned words/phrases (mlf-vault CLAUDE.md, case-insensitive). Edit here to
# add/remove terms; "stacked luxury adjectives" from the vault list is not
# included because it isn't a literal string match and needs human review.
BANNED_TERMS = [
    "silk",
    "artificial",
    "real touch",
    "bespoke",
    "cookie-cutter",
    "never generic",
    "artistic partner",
]

# The one file where dollar amounts are banned outright ("priced by inquiry"
# only, per mlf-vault CLAUDE.md).
NO_DOLLAR_FILENAME = "corporate-floral-design-chicago.html"
DOLLAR_PATTERN = re.compile(r"\$\d[\d,]*(?:\.\d+)?")

LUXE_PATTERN = re.compile(r"\bmodern\s+luxe\s+flowers\b", re.IGNORECASE)

DEFAULT_WHOLESALER_LIST_ENV = "MLF_WHOLESALER_LIST"
DEFAULT_WHOLESALER_LIST_PATH = Path(__file__).resolve().parent.parent / ".wholesalers-private.txt"


def _compile_banned_patterns():
    patterns = []
    for term in BANNED_TERMS:
        patterns.append((term, re.compile(r"\b" + re.escape(term) + r"\b", re.IGNORECASE)))
    return patterns


BANNED_PATTERNS = _compile_banned_patterns()


def load_wholesaler_names(path_override=None):
    """Load private wholesaler names, if a list has been supplied locally.

    Never store wholesaler names in this repo directly. This file is
    gitignored; populate it locally (one name per line) if you want this
    rule enforced. Absent file = rule silently skipped.
    """
    path = Path(path_override) if path_override else Path(
        os.environ.get(DEFAULT_WHOLESALER_LIST_ENV, DEFAULT_WHOLESALER_LIST_PATH)
    )
    if not path.is_file():
        return []
    names = []
    for line in path.read_text(encoding="utf-8").splitlines():
        name = line.strip()
        if name and not name.startswith("#"):
            names.append(name)
    return names


def scan(text, display_name, wholesaler_names=None):
    """Return a list of violation dicts for the given text."""
    violations = []
    lines = text.splitlines()
    wholesaler_patterns = [
        (name, re.compile(r"\b" + re.escape(name) + r"\b", re.IGNORECASE))
        for name in (wholesaler_names or [])
    ]
    basename = os.path.basename(display_name)
    check_dollars = basename == NO_DOLLAR_FILENAME

    for line_no, line in enumerate(lines, start=1):
        for term, pattern in BANNED_PATTERNS:
            for match in pattern.finditer(line):
                violations.append({
                    "file": display_name,
                    "line": line_no,
                    "rule": "banned-word",
                    "term": term,
                    "snippet": line.strip(),
                })

        for match in LUXE_PATTERN.finditer(line):
            matched_text = match.group(0)
            if "LUXE" not in matched_text:
                violations.append({
                    "file": display_name,
                    "line": line_no,
                    "rule": "luxe-capitalization",
                    "term": matched_text,
                    "snippet": line.strip(),
                })

        if check_dollars:
            for match in DOLLAR_PATTERN.finditer(line):
                violations.append({
                    "file": display_name,
                    "line": line_no,
                    "rule": "dollar-amount",
                    "term": match.group(0),
                    "snippet": line.strip(),
                })

        for name, pattern in wholesaler_patterns:
            for match in pattern.finditer(line):
                violations.append({
                    "file": display_name,
                    "line": line_no,
                    "rule": "wholesaler-name",
                    "term": name,
                    "snippet": line.strip(),
                })

    return violations


def format_violation(v):
    return f"{v['file']}:{v['line']}: [{v['rule']}] matched \"{v['term']}\" -> {v['snippet']}"


def main(argv=None):
    parser = argparse.ArgumentParser(description="MLF brand/content check")
    parser.add_argument("files", nargs="*", help="Files to check")
    parser.add_argument("--stdin", action="store_true", help="Read content from stdin")
    parser.add_argument("--name", help="Display name to use when reading from stdin")
    parser.add_argument("--json", action="store_true", help="Emit violations as JSON")
    parser.add_argument("--wholesaler-list", help="Path to a private wholesaler-name list")
    args = parser.parse_args(argv)

    if args.stdin and not args.name:
        parser.error("--stdin requires --name")
    if args.stdin and args.files:
        parser.error("--stdin cannot be combined with file arguments")
    if not args.stdin and not args.files:
        parser.error("provide FILE(s) or use --stdin --name NAME")

    wholesaler_names = load_wholesaler_names(args.wholesaler_list)

    all_violations = []
    if args.stdin:
        text = sys.stdin.read()
        all_violations.extend(scan(text, args.name, wholesaler_names))
    else:
        for file_path in args.files:
            try:
                text = Path(file_path).read_text(encoding="utf-8", errors="replace")
            except OSError as exc:
                print(f"{file_path}: could not read file ({exc})", file=sys.stderr)
                return 2
            all_violations.extend(scan(text, file_path, wholesaler_names))

    if args.json:
        print(json.dumps(all_violations, indent=2))
    else:
        for v in all_violations:
            print(format_violation(v))

    return 1 if all_violations else 0


if __name__ == "__main__":
    sys.exit(main())
