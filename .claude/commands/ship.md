# Ship to Main

Commit all current changes and push to the main branch. Follow every step in order.

## Step 1 — Understand what changed

Run these in parallel:
- `git status` — see all modified/untracked files (including untracked)
- `git diff` — see unstaged changes
- `git diff --cached` — see staged changes
- `git log --oneline -5` — see recent commit messages to match style

## Step 2 — Triage untracked files and update .gitignore if needed

Before staging anything, review every untracked file from `git status`.

For each untracked file or directory ask: *Should this ever be committed?*

Files that should NOT be committed (add to `.gitignore` if not already there):
- Dependency directories: `vendor/`, `node_modules/`, `.venv/`
- Build artefacts: `dist/`, `*.compiled.*`, `*.min.js` (if generated)
- Environment / secrets: `.env`, `*.local`, `*.key`, `*.pem`
- IDE / OS noise: `.DS_Store`, `.idea/`, `.vscode/`, `Thumbs.db`
- Runtime files: `*.log`, `*.cache`, `storage/logs/`
- Cloudflare tunnel credentials: `cloudflared/creds.json`

If any such files are present and missing from `.gitignore`:
1. Read the current `.gitignore`
2. Append the missing patterns
3. Run `git rm --cached <path>` for anything already tracked that should be ignored

Only then proceed to stage everything with `git add -A`.

## Step 3 — Draft the commit message

Write a commit message in this format:

```
<prefix>(<scope>): <short phase or feature summary>
- <bullet: specific thing added or changed>
- <bullet: specific thing added or changed>
- <bullet: specific thing added or changed>
...
```

Rules:
- First line: conventional prefix (`feat`, `fix`, `docs`, `refactor`, `chore`) + scope in parens + summary under 72 chars
- Bullets: one per logical change — be specific, not generic ("Add seller dashboard" not "Add files")
- Include a bullet for any `.gitignore` updates made in Step 2
- Do NOT include a `Co-Authored-By` trailer
- Match the tone and style of recent commits in the log

Example of a good message:
```
feat(seller): phase 3 — seller onboarding, product CRUD, R2 upload, Stripe Connect
- Seller onboarding form and shop profile creation
- Product CRUD with Cloudflare R2 image upload
- Stripe Connect account linking flow
- Seller dashboard and dedicated layout
- Add vendor/ to .gitignore; commit composer.json/lock only
- Remove redundant admin dashboard view stub
```

## Step 4 — Stage, commit, and push

Run sequentially:

```bash
git add -A
```

Then commit using a HEREDOC so formatting and bullet points are preserved:
```bash
git commit -m "$(cat <<'EOF'
<first line summary>
- bullet one
- bullet two
EOF
)"
```

Then push:
```bash
git push origin main
```

## Step 5 — Confirm

After the push succeeds, report:
- The commit hash and message (including bullets)
- How many files changed
- That the push to `origin main` completed

If `git push` fails (e.g. rejected due to diverged history), stop and tell the user — do not force push.
