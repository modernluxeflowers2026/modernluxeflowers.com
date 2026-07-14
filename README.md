# modernluxeflowers.com

## Brand/content pre-commit check

This repo has a pre-commit hook that blocks commits containing banned
words, incorrect "LUXE" capitalization, or dollar amounts in
`corporate-floral-design-chicago.html`. It lives in `.githooks/` (not
`.git/hooks/`, so it's version-controlled) and is implemented in
`scripts/brand_check.py`.

Enable it once per clone:

```
git config core.hooksPath .githooks
```

To also block specific wholesaler names, create a local
`.wholesalers-private.txt` in the repo root (one name per line — this file
is gitignored and must never be committed).

The same checker can run against a single file outside of git, e.g. for a
caption-approval step:

```
python3 scripts/brand_check.py path/to/caption.txt
python3 scripts/brand_check.py --json path/to/caption.txt   # machine-readable
```
