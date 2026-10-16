"""Builds deploy/windows/ZED-Staging-Deployment-Guide.docx (python-docx)."""
import sys
from docx import Document
from docx.enum.section import WD_ORIENT
from docx.enum.table import WD_TABLE_ALIGNMENT
from docx.enum.text import WD_ALIGN_PARAGRAPH, WD_BREAK
from docx.oxml import OxmlElement
from docx.oxml.ns import qn
from docx.shared import Pt, RGBColor, Cm

OUT = sys.argv[1]
GREEN = RGBColor(0x2E, 0x7D, 0x4F)
TEAL = RGBColor(0x1F, 0x6F, 0x8B)
GREY = RGBColor(0x55, 0x55, 0x55)

doc = Document()
sec = doc.sections[0]
sec.page_width, sec.page_height = Cm(21.0), Cm(29.7)
for side in ("left_margin", "right_margin"):
    setattr(sec, side, Cm(2.0))
sec.top_margin, sec.bottom_margin = Cm(1.8), Cm(1.8)

styles = doc.styles
styles["Normal"].font.name = "Calibri"
styles["Normal"].font.size = Pt(10.5)
styles["Normal"].element.rPr.rFonts.set(qn("w:eastAsia"), "Calibri")
for name, size, color in (("Heading 1", 16, GREEN), ("Heading 2", 13, TEAL), ("Heading 3", 11, TEAL)):
    st = styles[name]
    st.font.name = "Calibri"
    st.font.size = Pt(size)
    st.font.color.rgb = color
    st.element.rPr.rFonts.set(qn("w:eastAsia"), "Calibri")


def shade(element, fill):
    pr = element.get_or_add_pPr() if hasattr(element, "get_or_add_pPr") else element.get_or_add_tcPr()
    shd = OxmlElement("w:shd")
    shd.set(qn("w:val"), "clear")
    shd.set(qn("w:color"), "auto")
    shd.set(qn("w:fill"), fill)
    pr.append(shd)


def border_box(paragraph, color):
    pPr = paragraph._p.get_or_add_pPr()
    bdr = OxmlElement("w:pBdr")
    for side in ("top", "left", "bottom", "right"):
        b = OxmlElement(f"w:{side}")
        b.set(qn("w:val"), "single")
        b.set(qn("w:sz"), "6")
        b.set(qn("w:space"), "4")
        b.set(qn("w:color"), color)
        bdr.append(b)
    pPr.append(bdr)


def add_runs(p, text):
    """Text with **bold** and `code` spans."""
    import re
    for part in re.split(r"(\*\*[^*]+\*\*|`[^`]+`)", text):
        if not part:
            continue
        if part.startswith("**"):
            r = p.add_run(part[2:-2])
            r.bold = True
        elif part.startswith("`"):
            r = p.add_run(part[1:-1])
            r.font.name = "Consolas"
            r.font.size = Pt(9.5)
            r._element.rPr.rFonts.set(qn("w:eastAsia"), "Consolas")
        else:
            p.add_run(part)
    return p


def para(text, style=None, space_after=4):
    p = doc.add_paragraph(style=style)
    add_runs(p, text)
    p.paragraph_format.space_after = Pt(space_after)
    return p


def bullet(text, level=0):
    p = doc.add_paragraph(style="List Bullet" if level == 0 else "List Bullet 2")
    add_runs(p, text)
    p.paragraph_format.space_after = Pt(2)
    return p


_counter = [0]


def restart():
    _counter[0] = 0


def number(text):
    """Numbered step; restart() before each new list (Word's List Number keeps counting)."""
    _counter[0] += 1
    p = doc.add_paragraph()
    p.add_run(f"{_counter[0]}.	")
    add_runs(p, text)
    pf = p.paragraph_format
    pf.left_indent = Cm(0.9)
    pf.first_line_indent = Cm(-0.6)
    pf.tab_stops.add_tab_stop(Cm(0.9))
    pf.space_after = Pt(2)
    return p


def code(*lines):
    for i, line in enumerate(lines):
        p = doc.add_paragraph()
        r = p.add_run(line)
        r.font.name = "Consolas"
        r.font.size = Pt(9)
        r._element.rPr.rFonts.set(qn("w:eastAsia"), "Consolas")
        shade(p._p, "F2F4F5")
        pf = p.paragraph_format
        pf.space_before = Pt(0)
        pf.space_after = Pt(0) if i < len(lines) - 1 else Pt(6)
        pf.left_indent = Cm(0.4)


def note(text, kind="note"):
    fill, edge, label = {
        "note": ("EAF4FB", "1F6F8B", "Note: "),
        "warn": ("FFF4E5", "C77700", "Important: "),
        "ok": ("EAF6EE", "2E7D4F", "Result: "),
    }[kind]
    p = doc.add_paragraph()
    r = p.add_run(label)
    r.bold = True
    add_runs(p, text)
    border_box(p, edge)
    shade(p._p, fill)
    p.paragraph_format.space_after = Pt(8)
    p.paragraph_format.left_indent = Cm(0.2)
    p.paragraph_format.right_indent = Cm(0.2)


def table(header, rows, widths=None):
    t = doc.add_table(rows=1, cols=len(header))
    t.style = "Table Grid"
    t.alignment = WD_TABLE_ALIGNMENT.CENTER
    for i, h in enumerate(header):
        cell = t.rows[0].cells[i]
        cell.text = ""
        r = cell.paragraphs[0].add_run(h)
        r.bold = True
        r.font.color.rgb = RGBColor(0xFF, 0xFF, 0xFF)
        shade(cell._tc, "2E7D4F")
    for row in rows:
        cells = t.add_row().cells
        for i, value in enumerate(row):
            cells[i].text = ""
            add_runs(cells[i].paragraphs[0], value)
    for row in t.rows:
        for i, cell in enumerate(row.cells):
            for p in cell.paragraphs:
                p.paragraph_format.space_after = Pt(1)
                for r in p.runs:
                    if r.font.size is None:
                        r.font.size = Pt(9.5)
            if widths:
                cell.width = Cm(widths[i])
    doc.add_paragraph().paragraph_format.space_after = Pt(2)
    return t


def step(title):
    doc.add_heading(title, level=3)


# ---------------------------------------------------------------- title page
p = doc.add_paragraph()
p.paragraph_format.space_before = Pt(90)
r = p.add_run("ZED Website")
r.font.size = Pt(30)
r.bold = True
r.font.color.rgb = GREEN
p = doc.add_paragraph()
r = p.add_run("Staging deployment guide")
r.font.size = Pt(20)
r.font.color.rgb = TEAL
p = doc.add_paragraph()
r = p.add_run("zedcms.umon.in  |  Ubuntu (AWS) + nginx + PHP 8.4 + MySQL + free SSL certificate")
r.font.size = Pt(12)
r.font.color.rgb = GREY
doc.add_paragraph()
table(["Item", "Value"], [
    ["Website", "MSME Sustainable (ZED) Certification website and CMS (Laravel 13 version, `Code/zed-laravel`)"],
    ["Staging address", "http://zedcms.umon.in, then https:// with a free Let's Encrypt certificate"],
    ["Server", "Ubuntu Linux on AWS (Mumbai), public IP **13.232.227.197**. The server already hosts another "
               "website; the ZED site is added next to it without changing it."],
    ["Server login", "SSH, user `ubuntu`, with the key file (.pem) of the server"],
    ["Code", "https://github.com/mahavirtiwari/ZED-Website (private), branch `main`"],
    ["Scripts", "`deploy/windows/*.ps1` (run on your Windows PC) and `deploy/linux/*.sh` (run on the server for you)"],
    ["Document date", "16 October 2026"],
], widths=[4, 13])
para("Who this is for: the person deploying the website from a Windows PC (the Dell). You need the server's SSH key "
     "and access to the GitHub repository. You never have to type Linux commands for a normal deployment: two "
     "PowerShell commands on the Dell do everything.", space_after=6)
doc.add_paragraph().add_run().add_break(WD_BREAK.PAGE)

# ---------------------------------------------------------------- contents
doc.add_heading("Contents", level=1)
for line in [
    "1. How the deployment works",
    "2. Before you start (checklist)",
    "Part A - On your Dell: Git, the code, the SSH key and the release package",
    "Part B - First installation on the server (one command)",
    "Part C - Updating staging with a new version (and rollback)",
    "Part D - Day-to-day operation: logs, settings, backups, SSL certificate",
    "Part E - Troubleshooting",
    "Appendix - Script reference, server layout, code changes, security",
]:
    para(line, space_after=2)
doc.add_paragraph().add_run().add_break(WD_BREAK.PAGE)

# ---------------------------------------------------------------- 1
doc.add_heading("1. How the deployment works", level=1)
table(["Where", "What happens", "Script"], [
    ["GitHub", "Holds the code (website, CMS, database files, these scripts).", "-"],
    ["Your Dell (PowerShell)", "Downloads the latest code and packs it into one zip "
     "(`ZED-release-<date>-<commit>.zip`, about 65 MB).", "`Get-ZedCode.ps1`"],
    ["Your Dell (PowerShell)", "Uploads the zip to the server over SSH and runs the server scripts there.",
     "`Publish-ZedRelease.ps1`"],
    ["Ubuntu server (bash, started by Publish)", "First time: nginx site, PHP 8.4, MySQL database, free SSL certificate. "
     "Every release: installs the new version and switches over in seconds.",
     "`install-server.sh`, `init-database.sh`, `deploy.sh`, `enable-https.sh`"],
], widths=[3.6, 9, 4.4])
para("Each release is installed into its own folder. Settings, logs and uploaded files are kept in a separate "
     "**shared** folder, so an update never loses them, and the previous release can be brought back with one "
     "command (rollback).")
note("The server already runs another website (it answers with a *.qci.org.in certificate). The ZED scripts only "
     "**add**: their own nginx site for zedcms.umon.in, their own PHP-FPM pool and Linux user (`zedcms`), and a "
     "database in the existing MySQL if there is one. They never change other nginx sites, other PHP settings or "
     "the firewall, and every nginx change is tested (`nginx -t`) before it is applied.")

# ---------------------------------------------------------------- 2
doc.add_heading("2. Before you start (checklist)", level=1)
table(["#", "Check", "How / status"], [
    ["1", "You have the server's SSH key file (.pem) and the user name (AWS Ubuntu: `ubuntu`).",
     "From whoever created the server (AWS console > EC2 > Key pairs). Store it as `C:\\ZED\\keys\\zed-staging.pem`."],
    ["2", "AWS security group of the server allows inbound: **TCP 22** from your office IP, **TCP 80** and **TCP 443** "
          "from anywhere (0.0.0.0/0).",
     "AWS console > EC2 > Instances > the server > Security > Security groups > Edit inbound rules. "
     "On 16 Oct 2026 port 80 did not answer from outside: it must be opened for the free SSL certificate."],
    ["3", "DNS: the A record of **zedcms.umon.in** points to **13.232.227.197**.",
     "At the DNS provider of umon.in. On 16 Oct 2026 it still pointed to 20.219.21.200: **change it**. "
     "Check from the Dell: `nslookup zedcms.umon.in`"],
    ["4", "Ubuntu 22.04 or 24.04, at least 2 GB RAM and 10 GB free disk.", "The installer checks the version."],
    ["5", "You can open the private GitHub repository.", "Sign in at github.com; mahavirtiwari/ZED-Website must be visible."],
    ["6", "An e-mail address for the free Let's Encrypt SSL certificate account.", "Any monitored mailbox."],
    ["7", "(Recommended) A recent export of the production database and the uploaded files "
          "(uploads, slider, photogallery folders) from zed.msme.gov.in.",
     "Ask the production hosting team. Without them, staging uses the database copy in the repository (older content)."],
], widths=[0.8, 7.7, 8.5])
note("Items 2 and 3 are needed for the free SSL certificate. You can install the website before they are done "
     "(leave out `-HttpsEmail` in B2) and get the certificate later (B5).", "warn")

doc.add_heading("Images and uploaded files", level=2)
bullet("**Theme images** (logos, icons, backgrounds in `Code/zedcms/assets`) are in GitHub and come with the release "
       "package automatically.")
bullet("**Uploaded content** (what editors add in the CMS: news images, slider, gallery photos, PDFs) is not in GitHub. "
       "It travels separately as a **media package** (`ZED-media-<date>.zip`, step A4).")

# ---------------------------------------------------------------- A
doc.add_page_break()
doc.add_heading("Part A - On your Dell", level=1)
para("Use **Windows PowerShell** (Start menu > type PowerShell > Windows PowerShell). Administrator rights are not needed.")

step("A1. Install Git (first time only)")
code("git --version")
para("If you see \"git is not recognized\", install it, then close and reopen PowerShell:")
code("winget install --id Git.Git -e --source winget")
para("(Or download the installer from https://git-scm.com/download/win and accept the defaults.)")

step("A2. Download the code (first time only)")
code("New-Item -ItemType Directory -Force C:\\ZED, C:\\ZED\\keys | Out-Null",
     "git clone https://github.com/mahavirtiwari/ZED-Website.git C:\\ZED\\ZED-Website")
note("The repository is private. The first time, a GitHub sign-in window opens: choose \"Sign in with your browser\" "
     "and approve. Git remembers it afterwards.")

step("A3. SSH key and connection test (first time only)")
para("Copy the server's key file to `C:\\ZED\\keys\\zed-staging.pem`, then test the connection (type **yes** when asked "
     "whether to trust the server):")
code("ssh -i C:\\ZED\\keys\\zed-staging.pem ubuntu@13.232.227.197 \"hostname; lsb_release -ds\"")
para("It should print the server's name and Ubuntu version. If it says \"UNPROTECTED PRIVATE KEY FILE\", the publish "
     "script fixes the file permissions for you; you can also run "
     "`icacls C:\\ZED\\keys\\zed-staging.pem /inheritance:r /grant:r \"$($env:USERNAME):R\"`.")

step("A4. Build the release package (every release)")
code("powershell -ExecutionPolicy Bypass -File C:\\ZED\\ZED-Website\\deploy\\windows\\Get-ZedCode.ps1")
para("It downloads the newest code from GitHub (branch main) and ends with:")
code("Package ready: C:\\ZED\\packages\\ZED-release-20261016-1700-2c04118.zip (65 MB)")

step("A5. Build the media package (first deployment, and whenever you want to refresh uploaded files)")
para("Point the script at a folder that contains the `uploads`, `slider` and `photogallery` folders (for example a copy "
     "of the production web root you received from the hosting team):")
code("powershell -ExecutionPolicy Bypass -File C:\\ZED\\ZED-Website\\deploy\\windows\\Export-ZedMedia.ps1 -WebRoot D:\\zed-production-copy")
para("Result: `C:\\ZED\\packages\\ZED-media-<date>.zip`. PHP files are never included.")
note("The production server is Linux? The hosting team can make the same zip there: "
     "`cd /var/www/zed && zip -r ~/ZED-media.zip uploads slider photogallery -x '*.php'`")

# ---------------------------------------------------------------- B
doc.add_page_break()
doc.add_heading("Part B - First installation on the server", level=1)
para("Everything in Part B is started from the Dell in PowerShell. Go to the scripts folder first:")
code("cd C:\\ZED\\ZED-Website\\deploy\\windows",
     "Set-ExecutionPolicy -Scope Process Bypass -Force")
para("The second line allows the scripts to run in this PowerShell window only; repeat it in every new window.")

step("B1. Check DNS and ports (checklist items 2 and 3)")
code("nslookup zedcms.umon.in")
para("The answer must be 13.232.227.197. If not, change the DNS record and wait until it shows (usually minutes, "
     "at most a few hours).")

step("B2. Install everything (one command, 10-20 minutes)")
para("Replace the media file name and the e-mail addresses:")
code(".\\Publish-ZedRelease.ps1 -KeyFile C:\\ZED\\keys\\zed-staging.pem -Setup `",
     "    -MediaZip C:\\ZED\\packages\\ZED-media-20261016-1500.zip `",
     "    -AdminEmail your.name@example.com -HttpsEmail your.name@example.com")
para("With a production database export (copy it to the Dell first), add: `-DumpFile C:\\ZED\\packages\\zedcms-production.sql`")
para("What happens on the server:")
table(["Step", "Script", "Details"], [
    ["Upload", "-", "The package (and media zip / database export) go to `~/zed-deploy` on the server."],
    ["Server software", "`install-server.sh`", "nginx (if missing), PHP 8.4-FPM from ppa:ondrej/php with its own pool and "
     "user `zedcms`, Composer, MySQL (only if no MySQL/MariaDB is installed yet), certbot for the free SSL certificate."],
    ["Database", "`init-database.sh`", "Database `zedcms` (utf8mb4), import, upgrade scripts, MySQL user `zed` with a random "
     "password and access to this database only; settings written to `/var/www/zedcms/shared/.env`."],
    ["Website", "`deploy.sh`", "Copies the release, links settings/storage/uploads, installs PHP libraries (composer), "
     "runs database migrations, builds caches, writes the nginx site for zedcms.umon.in, switches the site to the new "
     "release, creates the CMS superadmin and checks that `/` and `/cms/login` answer."],
    ["Free SSL", "`enable-https.sh`", "Checks DNS, gets the Let's Encrypt certificate, switches the site to HTTPS and tests "
     "the automatic renewal."],
], widths=[2.8, 3.6, 10.6])

step("B3. What you are asked during B2")
bullet("\"Are you sure you want to continue connecting (yes/no)?\" - the first connection only: type **yes**.")
bullet("\"New password\" / \"Repeat the password\" - the password for the CMS superadmin (`-AdminEmail`): at least 10 "
       "characters with upper and lower case letters, a digit and a symbol. Not shown while typing.")
bullet("\"[sudo] password for ubuntu\" - only if the server account needs one (AWS Ubuntu normally does not).")
bullet("\"MySQL root password\" - only if the server's existing MySQL uses a root password.")
note("Deployed. Website: https://zedcms.umon.in/   CMS: https://zedcms.umon.in/cms", "ok")

step("B4. Check the site in a browser")
para("Open https://zedcms.umon.in/ (or http:// if the certificate was not requested yet). You should see the ZED home "
     "page with logos, slider, dashboard figures and the language (Bhashini) button at the top right.")

step("B5. Free SSL certificate (Let's Encrypt)")
para("If you left out `-HttpsEmail` in B2, or DNS/port 80 were not ready, get the certificate later with the same "
     "publish command plus `-HttpsEmail` (it re-installs the current package and then requests the certificate):")
code(".\\Publish-ZedRelease.ps1 -KeyFile C:\\ZED\\keys\\zed-staging.pem -HttpsEmail your.name@example.com")
table(["Free certificate facts", ""], [
    ["Cost", "Free (Let's Encrypt, a non-profit certificate authority), trusted by all browsers."],
    ["Valid for", "90 days (Let's Encrypt is gradually making this shorter)."],
    ["Renewal", "Automatic: certbot's timer on the server checks twice a day and renews about 30 days before expiry, then "
                "reloads nginx. Nothing to do by hand."],
    ["Requirement", "DNS keeps pointing to the server and port 80 stays open to the internet: every renewal is checked "
                    "over http (the site keeps that check path out of the https redirect)."],
    ["Expiry e-mails", "Let's Encrypt no longer sends them (since 2025). Check monthly: "
                       "`.\\Publish-ZedRelease.ps1 -KeyFile C:\\ZED\\keys\\zed-staging.pem -CertificateStatus`"],
], widths=[3.5, 13.5])

step("B6. Final checks")
table(["Check", "Expected"], [
    ["https://zedcms.umon.in/", "Home page loads with images; http:// redirects to https://."],
    ["Menu pages, news, gallery, videos", "Open normally; gallery shows the photos from the media package."],
    ["https://zedcms.umon.in/cms", "CMS login with captcha image and password eye button; sign in with the B2 account."],
    ["CMS: edit a page, upload an image", "Saved; the image shows on the website."],
    ["Contact Us", "Form appears (Zoho or the site's own form, CMS > Settings > Security)."],
    ["Language button (Bhashini)", "Choosing Hindi translates the page."],
    ["Dashboard figures", "Live numbers from the ZED portal (CMS numbers if the portal is unreachable)."],
    ["The other website on the server", "Still works as before."],
    ["Security headers", "https://securityheaders.com/?q=zedcms.umon.in shows Content-Security-Policy, HSTS, X-Frame-Options."],
], widths=[6, 11])
note("Staging does not send e-mail: messages (contact form, password reset) are written to "
     "`/var/www/zedcms/shared/storage/logs/laravel.log`. To send real mail, see Part D.")

# ---------------------------------------------------------------- C
doc.add_page_break()
doc.add_heading("Part C - Updating staging with a new version", level=1)
para("When new code has been pushed to GitHub, on the Dell:")
code("cd C:\\ZED\\ZED-Website\\deploy\\windows",
     "Set-ExecutionPolicy -Scope Process Bypass -Force",
     ".\\Get-ZedCode.ps1",
     ".\\Publish-ZedRelease.ps1 -KeyFile C:\\ZED\\keys\\zed-staging.pem")
para("Settings, uploaded files, sessions, logs and the SSL certificate are kept. The switch takes a few seconds. If "
     "anything fails before the switch, the site keeps running the previous release.")

doc.add_heading("Rollback", level=2)
code(".\\Publish-ZedRelease.ps1 -KeyFile C:\\ZED\\keys\\zed-staging.pem -Rollback")
para("Goes back to the previous release (the last 3 are kept on the server).")
note("Rollback changes the code only. Database changes made by a newer release stay (they are additive, so the older "
     "code still works).")

doc.add_heading("Refreshing uploaded files or data from production", level=2)
bullet("New uploaded files: make a new media zip (A5) and run Publish with `-MediaZip C:\\ZED\\packages\\ZED-media-<date>.zip`. "
       "Files are added; newer files already on staging are kept.")
bullet("Fresh data (replaces all staging content): copy the export to the server and run, in an SSH session (Part D): "
       "`sudo bash ~/zed-deploy/<latest package>/server/init-database.sh --replace --dump ~/zed-deploy/zedcms-production.sql`, "
       "then Publish again (runs the migrations). CMS accounts then come from production; reset yours with the command in Part D.")

# ---------------------------------------------------------------- D
doc.add_heading("Part D - Day-to-day operation", level=1)
para("For these tasks open an SSH session from the Dell:")
code("ssh -i C:\\ZED\\keys\\zed-staging.pem ubuntu@13.232.227.197")
table(["Task", "Command on the server"], [
    ["Website error log", "`tail -n 50 /var/www/zedcms/shared/storage/logs/laravel.log`"],
    ["nginx logs", "`/var/log/nginx/zedcms.error.log`, `/var/log/nginx/zedcms.access.log`"],
    ["Script logs", "`ls /var/log/zed-deploy` (one file per script run; send it to the developer when asking for help)"],
    ["Restart the website", "`sudo systemctl reload php8.4-fpm nginx`"],
    ["Change a setting", "`sudo nano /var/www/zedcms/shared/.env`, save, then: "
                         "`cd /var/www/zedcms/current && sudo -u zedcms php8.4 artisan optimize && sudo systemctl reload php8.4-fpm`"],
    ["Send real e-mail", "In `.env` set `MAIL_MAILER=smtp`, `MAIL_HOST`, `MAIL_PORT`, `MAIL_USERNAME`, `MAIL_PASSWORD`, "
                         "`MAIL_FROM_ADDRESS`; then the \"Change a setting\" commands."],
    ["Certificate check (Verify ZED Certificate page)", "`ZED_VERIFY_TOKEN` in `.env` - ask the ZED portal team."],
    ["New CMS admin / forgotten password", "`cd /var/www/zedcms/current && sudo -u zedcms php8.4 artisan zed:cms-admin someone@example.com`"],
    ["Database backup", "`sudo mysqldump --single-transaction zedcms | gzip > ~/zedcms-$(date +%F).sql.gz`"],
    ["Uploaded files backup", "`sudo tar czf ~/zed-media-$(date +%F).tar.gz -C /var/www/zedcms/shared media`"],
    ["SSL certificate status (monthly)", "From the Dell: `.\\Publish-ZedRelease.ps1 -KeyFile ... -CertificateStatus` "
                                         "(expiry date, days left, renewal timer)"],
    ["SSL certificate renewal", "Automatic (`systemctl list-timers certbot.timer`). Test: `sudo certbot renew --dry-run`"],
    ["Download a backup to the Dell", "On the Dell: `scp -i C:\\ZED\\keys\\zed-staging.pem ubuntu@13.232.227.197:zedcms-2026-10-16.sql.gz C:\\ZED\\`"],
], widths=[4.5, 12.5])

# ---------------------------------------------------------------- E
doc.add_heading("Part E - Troubleshooting", level=1)
table(["Problem", "Likely cause", "Fix"], [
    ["\"running scripts is disabled on this system\"", "PowerShell execution policy.",
     "`Set-ExecutionPolicy -Scope Process Bypass -Force` in the same window."],
    ["ssh: \"Connection timed out\"", "Port 22 closed for your IP.", "AWS security group: allow TCP 22 from your office IP."],
    ["ssh: \"Permission denied (publickey)\"", "Wrong key file or user.", "Use the server's .pem and user `ubuntu`."],
    ["\"Change the DNS A record of zedcms.umon.in\"", "DNS still points elsewhere.", "Update the A record to 13.232.227.197; wait; run Publish with `-HttpsEmail` again."],
    ["\"Let's Encrypt could not verify\"", "Port 80 closed from the internet.", "AWS security group: allow TCP 80 from 0.0.0.0/0."],
    ["Browser shows the other website or a certificate for *.qci.org.in", "DNS or the ZED nginx site missing.",
     "`nslookup zedcms.umon.in`; check that Publish finished; on the server `sudo nginx -T | grep zedcms.umon.in`."],
    ["502 Bad Gateway", "PHP-FPM pool not running.", "`sudo systemctl status php8.4-fpm`; `sudo systemctl restart php8.4-fpm`."],
    ["HTTP 500 error", "Settings or permissions.", "Read the end of `laravel.log` (Part D)."],
    ["Captcha image missing on the CMS login", "Old nginx configuration.", "Publish again (the ZED nginx site sends missing images to Laravel)."],
    ["Logos and icons missing", "Theme images missing from the package.", "Rebuild the package with Get-ZedCode.ps1 (it warns when images are missing)."],
    ["News or gallery images missing", "Uploaded files not on staging.", "Publish with `-MediaZip` (A5)."],
    ["Cannot stay signed in to the CMS on http://", "Secure cookies switched on without HTTPS.", "Finish B5, or set `SESSION_SECURE_COOKIE=false` (Part D)."],
    ["\"$'\\r': command not found\" on the server", "Windows line endings in a script.", "Publish converts them automatically; rebuild the package with Get-ZedCode.ps1."],
    ["Composer error during deploy", "No internet from the server.", "Allow outbound HTTPS in the AWS security group / network ACL; Publish again."],
], widths=[4.6, 4.2, 8.2])

# ---------------------------------------------------------------- Appendix
doc.add_page_break()
doc.add_heading("Appendix", level=1)
doc.add_heading("Script reference", level=2)
table(["Script", "Where", "Main options"], [
    ["`Get-ZedCode.ps1`", "Dell", "`-Branch main`, `-WorkDir C:\\ZED`"],
    ["`Export-ZedMedia.ps1`", "Dell", "`-WebRoot <folder with uploads, slider, photogallery>`, `-OutDir`"],
    ["`Publish-ZedRelease.ps1`", "Dell", "`-KeyFile`, `-Server 13.232.227.197`, `-User ubuntu`, `-Setup`, `-DumpFile`, "
                                         "`-MediaZip`, `-AdminEmail`, `-HttpsEmail`, `-Rollback`, `-CertificateStatus`"],
    ["`install-server.sh`", "Server (sudo)", "-"],
    ["`init-database.sh`", "Server (sudo)", "`--dump FILE`, `--skip-upgrade`, `--replace`"],
    ["`deploy.sh`", "Server (sudo)", "`--media ZIP`, `--admin-email`, `--https-email`, `--rollback`"],
    ["`enable-https.sh`", "Server (sudo)", "`--email`, `--status`, `--skip-dns-check`"],
], widths=[4.5, 3, 9.5])

doc.add_heading("Folders and files on the server", level=2)
code("/var/www/zedcms/current             link to the live release (nginx root: current/public)",
     "/var/www/zedcms/releases/<date>/    the last 3 releases",
     "/var/www/zedcms/shared/.env         settings (database, APP_KEY, mail, APP_URL)",
     "/var/www/zedcms/shared/storage/     logs, sessions, cache",
     "/var/www/zedcms/shared/media/       uploads, slider, photogallery",
     "/etc/nginx/sites-available/zedcms.conf       the ZED nginx site",
     "/etc/php/8.4/fpm/pool.d/zedcms.conf          the ZED PHP-FPM pool (user zedcms)",
     "/etc/letsencrypt/live/zedcms.umon.in/        the free SSL certificate",
     "/var/log/zed-deploy/                script logs",
     "~/zed-deploy/                       uploaded packages (last 3)")

doc.add_heading("Code changes made for the staging server", level=2)
bullet("`deploy/windows` (PowerShell, Dell) and `deploy/linux` (bash, server) - the scripts in this guide, plus nginx "
       "templates. Missing image files are passed to Laravel, so the captcha image (`/captcha/login.png`) works; the "
       "same fix was made in `deploy/nginx/zed-laravel.conf`.")
bullet("`php artisan zed:cms-admin <email>` - creates a CMS superadmin or resets its password from the command line.")
bullet("`zedcms.umon.in` added to the known site addresses (`config/zed.php`).")
bullet("`.gitattributes` keeps Unix line endings for the server scripts on Windows checkouts.")

doc.add_heading("Security notes", level=2)
bullet("The ZED PHP code runs as its own user `zedcms` in its own PHP-FPM pool, limited to `/var/www/zedcms` "
       "(open_basedir); other websites on the server are separate.")
bullet("MySQL listens on 127.0.0.1 only; the website uses its own MySQL user limited to the zedcms database.")
bullet("`.env` (passwords, keys) lies outside the web folder and is readable only by root and the zedcms user; nginx "
       "refuses dot-files and backup/config file types.")
bullet("Only `index.php` runs PHP; uploaded files can never run as scripts and PHP files are dropped from media packages.")
bullet("The application sends the strict Content-Security-Policy and other security headers itself (security audit "
       "Verification Report No. I); HTTPS uses TLS 1.2/1.3 with HSTS.")
bullet("Restrict SSH (port 22) in the AWS security group to your office IP; keep the .pem key private.")

# footer with page numbers
for s in doc.sections:
    fp = s.footer.paragraphs[0]
    fp.alignment = WD_ALIGN_PARAGRAPH.CENTER
    r = fp.add_run("ZED Website - staging deployment guide - page ")
    r.font.size = Pt(8)
    r.font.color.rgb = GREY
    run = fp.add_run()
    for tag, text in (("begin", None), (None, "PAGE"), ("end", None)):
        if tag:
            el = OxmlElement("w:fldChar")
            el.set(qn("w:fldCharType"), tag)
        else:
            el = OxmlElement("w:instrText")
            el.set(qn("xml:space"), "preserve")
            el.text = text
        run._r.append(el)
    run.font.size = Pt(8)

doc.save(OUT)
print("saved", OUT)
