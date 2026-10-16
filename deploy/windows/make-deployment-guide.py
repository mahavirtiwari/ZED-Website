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
r = p.add_run("zedcms.umon.in  |  Windows Server (Azure) + IIS + PHP 8.4 + MySQL 8.4")
r.font.size = Pt(12)
r.font.color.rgb = GREY
doc.add_paragraph()
table(["Item", "Value"], [
    ["Website", "MSME Sustainable (ZED) Certification website and CMS (Laravel 13 version, `Code/zed-laravel`)"],
    ["Staging address", "http://zedcms.umon.in (https:// after step B6)"],
    ["Server", "Windows Server on Azure, public IP 20.219.21.200 (DNS already points here)"],
    ["Code", "https://github.com/mahavirtiwari/ZED-Website (private), branch `main`"],
    ["Scripts", "`deploy/windows/*.ps1` in the repository"],
    ["Document date", "16 October 2026"],
], widths=[4, 13])
para("Who this is for: the person deploying the website. You need administrator access to the staging server "
     "(Remote Desktop) and access to the GitHub repository. No programming knowledge is needed: every step is a "
     "command to copy and paste.", space_after=6)
doc.add_paragraph().add_run().add_break(WD_BREAK.PAGE)

# ---------------------------------------------------------------- contents
doc.add_heading("Contents", level=1)
for line in [
    "1. How the deployment works",
    "2. Before you start (checklist)",
    "Part A - On your computer (Dell): get the code and build the package",
    "Part B - On the staging server: first installation",
    "Part C - Updating staging with a new version (and rollback)",
    "Part D - Day-to-day operation: logs, settings, backups",
    "Part E - Troubleshooting",
    "Appendix - Script reference, server layout, code changes for staging",
]:
    para(line, space_after=2)
doc.add_paragraph().add_run().add_break(WD_BREAK.PAGE)

# ---------------------------------------------------------------- 1
doc.add_heading("1. How the deployment works", level=1)
para("The website is deployed in three places, always in this order:")
table(["Where", "What happens", "Script"], [
    ["GitHub", "Holds the code (website, CMS, database files, these scripts).", "-"],
    ["Your Dell computer", "Downloads the latest code from GitHub and packs it into one zip file "
     "(`ZED-release-<date>-<commit>.zip`, about 90 MB).", "`Get-ZedCode.ps1`"],
    ["Staging server", "First time: installs IIS, PHP, MySQL, creates the database. Every release: installs the "
     "zip as the website and switches over in a few seconds.",
     "`Install-ZedServer.ps1`, `Initialize-ZedDatabase.ps1`, `Deploy-ZedSite.ps1`, `Enable-ZedHttps.ps1`"],
], widths=[3.2, 9.3, 4.5])
para("Each release is installed into its own folder. Settings, logs and uploaded files are kept in a separate "
     "**shared** folder, so an update never loses them, and the previous release can be brought back with one "
     "command (rollback).")
note("The scripts download the software they install (PHP, MySQL, IIS URL Rewrite, Composer, Let's Encrypt client) "
     "from the official websites, so the server needs internet access while you run them.")

# ---------------------------------------------------------------- 2
doc.add_heading("2. Before you start (checklist)", level=1)
table(["#", "Check", "How"], [
    ["1", "You can sign in to the server with Remote Desktop as an administrator.",
     "Remote Desktop Connection (mstsc) to 20.219.21.200, or via the Azure portal (Connect > RDP)."],
    ["2", "The server is Windows Server 2019, 2022 or 2025, with at least 4 GB RAM and 20 GB free disk on C:.",
     "On the server: Settings > System > About."],
    ["3", "DNS: zedcms.umon.in points to the server.", "Already done (20.219.21.200). Check: `nslookup zedcms.umon.in`"],
    ["4", "Azure network security group allows inbound TCP 80 and 443 from the internet "
          "(and 3389 for Remote Desktop only from your office IP).",
     "Azure portal > the VM > Networking > Inbound port rules > Add."],
    ["5", "Port 80/443 not used by another program on the server.",
     "If IIS already hosts other sites that is fine: this site gets its own host name binding."],
    ["6", "You can open the private GitHub repository.", "Sign in at github.com; mahavirtiwari/ZED-Website must be visible."],
    ["7", "An e-mail address for certificate expiry notices (Let's Encrypt).", "Any monitored mailbox."],
    ["8", "(Recommended) A recent export of the production database and the uploaded files "
          "(uploads, slider, photogallery folders) from zed.msme.gov.in.",
     "Ask the production hosting team. Without them, staging uses the database copy in the repository "
     "(older content) and shows only the uploaded files that are in the media package."],
], widths=[0.8, 8.2, 8])

doc.add_heading("Images and uploaded files", level=2)
para("Two kinds of files are needed besides the code:")
bullet("**Theme images** (logos, icons, backgrounds in `Code/zedcms/assets`). They are in GitHub and come with the "
       "release package automatically.")
bullet("**Uploaded content** (what editors add in the CMS: news images, slider, gallery photos, PDFs). These are not in "
       "GitHub (about 360 MB, and they change on the live site). They travel separately as a **media package** "
       "(`ZED-media-<date>.zip`, step A4).")

# ---------------------------------------------------------------- A
doc.add_page_break()
doc.add_heading("Part A - On your computer (Dell)", level=1)
para("Do this in **Windows PowerShell** on your Dell (Start menu > type PowerShell > Windows PowerShell). "
     "Administrator rights are not needed.")

step("A1. Install Git (first time only)")
para("Check whether Git is installed:")
code("git --version")
para("If you see \"git is not recognized\", install it, then close and reopen PowerShell:")
code("winget install --id Git.Git -e --source winget")
para("(Or download the installer from https://git-scm.com/download/win and accept the defaults.)")

step("A2. Download the code (first time only)")
code("New-Item -ItemType Directory -Force C:\\ZED | Out-Null",
     "git clone https://github.com/mahavirtiwari/ZED-Website.git C:\\ZED\\ZED-Website")
note("The repository is private. The first time, a GitHub sign-in window opens: choose \"Sign in with your browser\" "
     "and approve. Git remembers it afterwards.")

step("A3. Build the release package (every release)")
code("powershell -ExecutionPolicy Bypass -File C:\\ZED\\ZED-Website\\deploy\\windows\\Get-ZedCode.ps1")
para("The script downloads the newest code from GitHub (branch main) and creates the package. It ends with:")
code("Package ready: C:\\ZED\\packages\\ZED-release-20261016-1700-2c04118.zip (90 MB)")
note("If it warns \"Only N theme images found\", the theme images are not in GitHub yet. Ask the developer to commit "
     "them (see Part E) before deploying, otherwise logos and icons are missing.", "warn")

step("A4. Build the media package (first deployment, and whenever you want to refresh uploaded files)")
para("Point the script at a folder that contains the `uploads`, `slider` and `photogallery` folders, for example a "
     "copy of the production web root that you received from the hosting team:")
code("powershell -ExecutionPolicy Bypass -File C:\\ZED\\ZED-Website\\deploy\\windows\\Export-ZedMedia.ps1 -WebRoot D:\\zed-production-copy")
para("Result: `C:\\ZED\\packages\\ZED-media-<date>.zip`. PHP files are never included (uploads must not contain scripts).")
note("If the production server is Linux, the hosting team can create the same zip there: "
     "`cd /var/www/zed && zip -r ~/ZED-media.zip uploads slider photogallery -x '*.php'`")

step("A5. Copy the packages to the server")
restart()
number("Open Remote Desktop Connection, enter 20.219.21.200, click **Show Options > Local Resources > More...**, "
       "tick **Drives** (your C: drive) and connect.")
number("On the server, open File Explorer and create the folder `C:\\ZED-setup`.")
number("In File Explorer on the server, open **This PC > C on <your Dell>**, go to `C:\\ZED\\packages` and copy "
       "the release zip (and the media zip) into `C:\\ZED-setup`.")
para("(Copy and paste between the two desktops also works, but is slower for large files.)")

# ---------------------------------------------------------------- B
doc.add_page_break()
doc.add_heading("Part B - On the staging server: first installation", level=1)
para("All commands in Part B run on the server in **PowerShell as administrator**: Start > type PowerShell > "
     "right-click **Windows PowerShell** > **Run as administrator**.")

step("B1. Unpack the release package")
para("Replace the file name with the one you copied (press Tab to complete names):")
code("cd C:\\ZED-setup",
     "Unblock-File .\\ZED-release-20261016-1700-2c04118.zip",
     "Expand-Archive .\\ZED-release-20261016-1700-2c04118.zip -DestinationPath .\\ZED-release-20261016-1700-2c04118",
     "cd .\\ZED-release-20261016-1700-2c04118\\scripts",
     "Set-ExecutionPolicy -Scope Process Bypass -Force")
para("The last line allows the scripts to run in this PowerShell window only. Repeat it whenever you open a new window.")

step("B2. Install the server software (once, 10-20 minutes)")
code(".\\Install-ZedServer.ps1")
para("It installs and configures:")
table(["Component", "Details"], [
    ["IIS", "Web server with CGI, compression, request filtering, IIS Manager."],
    ["IIS URL Rewrite 2.1", "Needed for the website's addresses (all pages go through index.php)."],
    ["Visual C++ runtime", "Required by PHP and MySQL."],
    ["PHP 8.4 (latest)", "`C:\\PHP`, non-thread-safe build, registered in IIS as FastCGI. php.ini: required extensions, "
                         "OPcache, 24 MB uploads, Asia/Kolkata time zone, CA certificates for the ZED portal APIs."],
    ["Composer", "`C:\\PHP\\composer.phar` (installs the website's PHP libraries)."],
    ["Firewall", "Inbound TCP 80 and 443 allowed in Windows Firewall."],
    ["MySQL 8.4 LTS", "`C:\\MySQL`, Windows service **MySQL84**, listening on 127.0.0.1 only (not reachable from outside)."],
], widths=[4, 13])
para("Near the end it asks you to choose the **MySQL root password** (typed twice, not shown). Write it down in your "
     "password manager; you need it in the next step.")
note("If the script says Windows needs a restart, restart the server, open PowerShell as administrator again and "
     "continue with B3 (repeat the `cd` and `Set-ExecutionPolicy` lines from B1).", "warn")
note("MySQL already installed on this server, or a separate database server? Run `.\\Install-ZedServer.ps1 -SkipMySql` "
     "instead.")

step("B3. Create the database (once)")
para("Using the database copy that comes with the code:")
code(".\\Initialize-ZedDatabase.ps1")
para("Or, recommended, with a recent production export (copy the .sql file to C:\\ZED-setup first):")
code(".\\Initialize-ZedDatabase.ps1 -DumpFile C:\\ZED-setup\\zedcms-production.sql")
para("It asks for the MySQL root password, then:")
bullet("creates the database `zedcms` (utf8mb4) and imports the dump;")
bullet("applies `upgrade_mysql8.sql` and `upgrade_2026_10_security.sql` (skip with `-SkipUpgradeScripts` if the export "
       "comes from a server that already runs this version);")
bullet("creates the MySQL user `zed` with a random password and access to `zedcms` only;")
bullet("writes the connection settings to `C:\\inetpub\\zedcms\\shared\\.env` (you never need to type that password).")
note("Running it again later keeps the existing database. To start over with fresh data: "
     "`.\\Initialize-ZedDatabase.ps1 -Replace` (asks you to type DELETE to confirm).")

step("B4. Deploy the website")
para("Replace the media file name and the e-mail address. The e-mail becomes a CMS superadmin; you choose its "
     "password when asked (at least 10 characters with upper and lower case letters, a digit and a symbol).")
code(".\\Deploy-ZedSite.ps1 -MediaZip C:\\ZED-setup\\ZED-media-20261016-1500.zip -AdminEmail your.name@example.com")
para("What it does, in order:")
restart()
number("copies the website to `C:\\inetpub\\zedcms\\releases\\<date-time>`;")
number("links the shared settings (`.env`), storage (logs, sessions) and uploaded files;")
number("installs the PHP libraries (composer), runs the database migrations and builds Laravel's caches;")
number("creates the IIS app pool **zedcms** (no .NET) and the website **zedcms** bound to `zedcms.umon.in` on port 80, "
       "and sets file permissions;")
number("switches `C:\\inetpub\\zedcms\\current` to the new release and checks that `/` and `/cms/login` answer HTTP 200.")
note("Deployed. Website: http://zedcms.umon.in/   CMS: http://zedcms.umon.in/cms", "ok")

step("B5. Check the site in a browser")
para("Open http://zedcms.umon.in/ from your Dell. You should see the ZED home page with logos, slider, dashboard "
     "figures and the language (Bhashini) button at the top right.")

step("B6. Switch on HTTPS (once)")
code(".\\Enable-ZedHttps.ps1 -Email your.name@example.com")
para("It downloads win-acme, gets a free Let's Encrypt certificate for zedcms.umon.in, adds the https binding in IIS "
     "and then switches the website to HTTPS: `APP_URL=https://zedcms.umon.in`, secure cookies, and every http "
     "address redirects to https. win-acme renews the certificate automatically (Task Scheduler).")
note("Let's Encrypt must reach http://zedcms.umon.in/ from the internet. If it fails, check the Azure inbound rule "
     "for port 80 (checklist item 4).", "warn")
note("Using your own certificate (for example *.umon.in)? Import the .pfx on the server (double-click > Local Machine), "
     "in IIS Manager open site **zedcms** > Bindings > Add > https, host name zedcms.umon.in, choose the certificate, "
     "then run `.\\Enable-ZedHttps.ps1 -SkipCertificate`.")

step("B7. Final checks")
table(["Check", "Expected"], [
    ["https://zedcms.umon.in/", "Home page loads with images; http:// redirects to https://."],
    ["Menu pages, news, gallery, videos", "Open normally; gallery shows the photos from the media package."],
    ["https://zedcms.umon.in/cms", "CMS login with captcha and password eye button; sign in with the B4 account."],
    ["CMS: edit a page, upload an image", "Saved; the image shows on the website."],
    ["Contact Us", "Form appears (Zoho or the site's own form, CMS > Settings > Security)."],
    ["Language button (Bhashini)", "Choosing Hindi translates the page."],
    ["Dashboard figures on the home page", "Live numbers from the ZED portal (CMS numbers if the portal is unreachable)."],
    ["Security headers", "https://securityheaders.com/?q=zedcms.umon.in shows Content-Security-Policy, HSTS, X-Frame-Options."],
], widths=[6, 11])
note("Staging does not send e-mail: messages (contact form, password reset) are written to "
     "`C:\\inetpub\\zedcms\\shared\\storage\\logs\\laravel.log`. To send real mail, see Part D.")

# ---------------------------------------------------------------- C
doc.add_page_break()
doc.add_heading("Part C - Updating staging with a new version", level=1)
para("When new code has been pushed to GitHub:")
restart()
number("On the Dell: `powershell -ExecutionPolicy Bypass -File C:\\ZED\\ZED-Website\\deploy\\windows\\Get-ZedCode.ps1`")
number("Copy the new `ZED-release-*.zip` to `C:\\ZED-setup` on the server (A5).")
number("On the server, PowerShell as administrator, unpack it and deploy (no media or admin options needed):")
code("cd C:\\ZED-setup",
     "Unblock-File .\\ZED-release-20261020-1000-abcd123.zip",
     "Expand-Archive .\\ZED-release-20261020-1000-abcd123.zip -DestinationPath .\\ZED-release-20261020-1000-abcd123",
     "cd .\\ZED-release-20261020-1000-abcd123\\scripts",
     "Set-ExecutionPolicy -Scope Process Bypass -Force",
     ".\\Deploy-ZedSite.ps1")
para("Settings, uploaded files, sessions and logs are kept. The switch takes a few seconds. If anything fails before "
     "the switch, the site keeps running the previous release.")

doc.add_heading("Rollback", level=2)
para("To go back to the previous release (the last 3 are kept):")
code("cd C:\\ZED-setup\\<any release folder>\\scripts",
     "Set-ExecutionPolicy -Scope Process Bypass -Force",
     ".\\Deploy-ZedSite.ps1 -Rollback")
note("Rollback changes the code only. Database changes made by a newer release stay (they are designed to be "
     "additive, so the older code still works).")

doc.add_heading("Refreshing uploaded files or data from production", level=2)
bullet("New uploaded files: make a new media zip (A4) and run `.\\Deploy-ZedSite.ps1 -MediaZip C:\\ZED-setup\\ZED-media-<date>.zip`. "
       "Files are added; newer files already on staging are kept.")
bullet("Fresh data: `.\\Initialize-ZedDatabase.ps1 -Replace -DumpFile C:\\ZED-setup\\zedcms-production.sql`, then "
       "`.\\Deploy-ZedSite.ps1` (runs the migrations). CMS accounts then come from production; reset yours with the "
       "command in Part D.")

# ---------------------------------------------------------------- D
doc.add_heading("Part D - Day-to-day operation", level=1)
table(["Task", "How (PowerShell as administrator)"], [
    ["Website error log", "`C:\\inetpub\\zedcms\\shared\\storage\\logs\\laravel.log`"],
    ["PHP error log", "`C:\\PHP\\logs\\php-errors.log`"],
    ["IIS request logs", "`C:\\inetpub\\logs\\LogFiles\\W3SVC<site id>`"],
    ["Script logs", "`C:\\ZED-setup\\logs` (one file per script run; send it to the developer when asking for help)"],
    ["Restart the website", "`Restart-WebAppPool zedcms`"],
    ["Change a setting", "Edit `C:\\inetpub\\zedcms\\shared\\.env` in Notepad, then: "
                         "`cd C:\\inetpub\\zedcms\\current; php artisan optimize; Restart-WebAppPool zedcms`"],
    ["Send real e-mail", "In `.env` set `MAIL_MAILER=smtp`, `MAIL_HOST`, `MAIL_PORT`, `MAIL_USERNAME`, `MAIL_PASSWORD`, "
                         "`MAIL_FROM_ADDRESS`; then the \"Change a setting\" commands."],
    ["Certificate check", "`ZED_VERIFY_TOKEN` in `.env` (Verify ZED Certificate page) - ask the ZED portal team."],
    ["New CMS admin / forgotten password", "`cd C:\\inetpub\\zedcms\\current; php artisan zed:cms-admin someone@example.com`"],
    ["Database backup", "`& \"$env:ZED_MYSQL_BIN\\mysqldump.exe\" -u root -p --single-transaction --default-character-set=utf8mb4 --result-file=C:\\ZED-setup\\backup-zedcms.sql zedcms` "
                        "(use --result-file, not >, which would save the file as UTF-16)"],
    ["Uploaded files backup", "Copy `C:\\inetpub\\zedcms\\shared\\media`"],
    ["Certificate renewal", "Automatic (Task Scheduler > win-acme renew). Manual: `C:\\win-acme\\wacs.exe --renew`"],
    ["Update PHP to the newest 8.4", "Run `.\\Install-ZedServer.ps1` again from the latest scripts folder."],
], widths=[4.5, 12.5])

# ---------------------------------------------------------------- E
doc.add_heading("Part E - Troubleshooting", level=1)
table(["Problem", "Likely cause", "Fix"], [
    ["\"running scripts is disabled on this system\"", "PowerShell execution policy.",
     "Run `Set-ExecutionPolicy -Scope Process Bypass -Force` in the same window."],
    ["\"Run this script ... as administrator\"", "PowerShell not elevated.", "Right-click Windows PowerShell > Run as administrator."],
    ["Browser shows \"HTTP Error 404\" from Microsoft-HTTPAPI", "No IIS site has the host name zedcms.umon.in.",
     "Run `Deploy-ZedSite.ps1`; in IIS Manager check site zedcms > Bindings."],
    ["All pages except the home page give 404", "URL Rewrite missing.", "Run `Install-ZedServer.ps1` again; restart IIS (`iisreset`)."],
    ["HTTP 500 error", "Settings or permission problem.", "Read the last lines of `laravel.log` and `php-errors.log` (Part D)."],
    ["\"could not find driver\" / database connection refused", "pdo_mysql missing or MySQL stopped.",
     "`Start-Service MySQL84`; `php -m` must list pdo_mysql."],
    ["Logos and icons missing", "Theme images not in GitHub.", "Developer: commit `Code/zedcms/assets` images; rebuild the package."],
    ["News or gallery images missing", "Uploaded files not on staging.", "Deploy a media package (A4, `-MediaZip`)."],
    ["Cannot stay signed in to the CMS on http://", "Secure cookies switched on without HTTPS.",
     "Run `Enable-ZedHttps.ps1`, or set `SESSION_SECURE_COOKIE=false` in `.env` (Part D)."],
    ["Let's Encrypt fails", "Port 80 not reachable from the internet.", "Azure NSG inbound rule for TCP 80; Windows Firewall rule \"ZED website\"."],
    ["Dashboard shows CMS numbers instead of live ones", "Server cannot reach loginzed.msme.gov.in.",
     "Check outbound internet; figures are cached for 10 minutes."],
    ["Composer error during deploy", "No internet, or GitHub/Packagist blocked.", "Allow outbound HTTPS; run Deploy again."],
], widths=[4.6, 4.6, 7.8])

# ---------------------------------------------------------------- Appendix
doc.add_page_break()
doc.add_heading("Appendix", level=1)
doc.add_heading("Script reference", level=2)
table(["Script", "Where", "Main options"], [
    ["`Get-ZedCode.ps1`", "Dell", "`-Branch main`, `-WorkDir C:\\ZED`"],
    ["`Export-ZedMedia.ps1`", "Dell / any Windows PC", "`-WebRoot <folder with uploads, slider, photogallery>`, `-OutDir`"],
    ["`Install-ZedServer.ps1`", "Server (admin)", "`-SkipMySql`, `-MySqlVersion 8.4.11`, `-PhpVersion 8.4`"],
    ["`Initialize-ZedDatabase.ps1`", "Server (admin)", "`-DumpFile`, `-SkipUpgradeScripts`, `-Replace`"],
    ["`Deploy-ZedSite.ps1`", "Server (admin)", "`-MediaZip`, `-AdminEmail`, `-Rollback`, `-HostName`"],
    ["`Enable-ZedHttps.ps1`", "Server (admin)", "`-Email`, `-SkipCertificate`"],
], widths=[5, 4, 8])

doc.add_heading("Folders on the server", level=2)
code("C:\\inetpub\\zedcms\\current\\           link to the live release (IIS site physical path: current\\public)",
     "C:\\inetpub\\zedcms\\releases\\<date>\\   the last 3 releases",
     "C:\\inetpub\\zedcms\\shared\\.env         settings (database, APP_KEY, mail, APP_URL)",
     "C:\\inetpub\\zedcms\\shared\\storage\\    logs, sessions, cache",
     "C:\\inetpub\\zedcms\\shared\\media\\      uploads, slider, photogallery",
     "C:\\PHP\\                              PHP 8.4, php.ini, composer.phar, logs",
     "C:\\MySQL\\                            MySQL 8.4 program, data, my.ini (service MySQL84)",
     "C:\\ZED-setup\\                        packages, downloads, script logs",
     "C:\\win-acme\\                         Let's Encrypt client")

doc.add_heading("Code changes made for the staging server", level=2)
bullet("`Code/zed-laravel/public/web.config` - IIS rules: all addresses go to Laravel, no trailing slashes, HTTPS "
       "redirect (switched on by the scripts), 32 MB request limit, no directory listing, no X-Powered-By / Server "
       "headers, and no script execution in `uploads`, `slider`, `photogallery` and `assets`.")
bullet("`php artisan zed:cms-admin <email>` - creates a CMS superadmin or resets its password from the command line.")
bullet("`zedcms.umon.in` added to the known site addresses (`config/zed.php`), so image links saved on staging also "
       "work after copying content elsewhere.")
bullet("`deploy/windows/*.ps1` - the scripts in this guide.")

doc.add_heading("Security notes", level=2)
bullet("MySQL listens on 127.0.0.1 only; the website uses its own MySQL user limited to the zedcms database.")
bullet("`.env` (passwords, keys) lies outside the web folder; IIS also refuses to serve `.env` and `.git`.")
bullet("Uploaded files cannot run as scripts; PHP files are dropped from media packages.")
bullet("The application sends the strict Content-Security-Policy and other security headers itself (security audit "
       "Verification Report No. I).")
bullet("Restrict Remote Desktop (port 3389) in Azure to your office IP address.")

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
