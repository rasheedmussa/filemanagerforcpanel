<div align="center">

# 🐘 TemboDrive

**Meneja wa Faili wa Kisasa kwa cPanel / Web Hosting**
**Modern File Manager for cPanel / Web Hosting**

Mfumo mwepesi, bila database, uliojengwa kwa PHP na JavaScript safi.
Unasaidia dark mode, lugha mbili (Kiswahili & English), drag-and-drop upload, na zaidi.

[![PHP](https://img.shields.io/badge/PHP-8.0+-777BB4?style=flat-square&logo=php&logoColor=white)](https://php.net)
[![License](https://img.shields.io/badge/License-MIT-22c55e?style=flat-square)](LICENSE)
[![No Database](https://img.shields.io/badge/Database-Haihitajiki-6366f1?style=flat-square)]()
[![Dark Mode](https://img.shields.io/badge/Dark%20Mode-Supported-0f1117?style=flat-square)]()

</div>

---

## ✨ Vipengele / Features

| | Kipengele | Maelezo |
|---|---|---|
| 🌙 | **Dark / Light Mode** | Badilisha wakati wowote — hali inakumbukwa |
| 🌐 | **Lugha Mbili** | UI kamili kwa **Kiswahili** na **English** |
| ☁️ | **Drag & Drop Upload** | Buruta faili au bonyeza kuchagua, progress bar ya moja kwa moja |
| 📁 | **Folder Upload** | Pakia miundo kamili ya folda |
| 🔍 | **Tafuta & Panga** | Filter faili mara moja, panga kwa jina, tarehe, au ukubwa |
| 👁️ | **Preview ya Faili** | Onyesha picha, PDF, na faili za maandishi ndani ya ukurasa |
| ✏️ | **Rename / Delete** | Simamia faili moja kwa moja kutoka kwa kivinjari |
| 📦 | **Bila Database** | 100% filesystem-based — hakuna usanidi |
| 📱 | **Responsive** | Inafanya kazi vizuri kwenye desktop, tablet, na simu |
| 🔔 | **Toast Notifications** | Ujumbe wa kisasa badala ya popup za zamani |

---

## 🚀 Usakinishaji / Installation

### Mahitaji / Requirements

- PHP **8.0+**
- Seva ya wavuti: Apache, Nginx, au cPanel
- Saraka ya `uploads/` yenye ruhusa ya kuandika

### Hatua / Steps

```bash
# 1. Clone au pakia mradi kwenye seva yako
git clone https://github.com/rasheedmussa/TemboDrive.git

# 2. Nenda kwenye folda ya mradi
cd tembodrive

# 3. Tengeneza saraka ya uploads na weka ruhusa
mkdir -p uploads
chmod 755 uploads
```

Kisha tembelea `http://yourdomain.com/tembodrive/` — tayari!

---

## ⚙️ PHP Config kwa Upakiaji Mkubwa / For Large Uploads

Kwenye cPanel → **MultiPHP INI Editor**, weka:

```ini
upload_max_filesize = 1024M
post_max_size       = 1024M
max_execution_time  = 300
max_input_time      = 300
memory_limit        = 512M
```

---

## 📁 Muundo wa Mradi / Project Structure

```
tembodrive/
├── assets/
│   ├── css/
│   │   └── style.css        # Mandhari ya kisasa (dark/light)
│   └── js/
│       └── main.js          # Mantiki ya UI, i18n, upload, modals
│
├── uploads/                 # Faili zilizopakiwa zinahifadhiwa hapa
│
├── index.php                # Mlango mkuu → inaelekeza dashboard
├── dashboard.php            # UI kuu ya meneja wa faili
├── config.php               # Msaada wa filesystem (bila DB)
├── upload.php               # Kushughulikia upakiaji wa faili/folda
├── download.php             # Kushughulikia upakuaji
├── delete.php               # Kufuta faili/folda
├── rename.php               # Kubadilisha jina la faili/folda
├── create_folder.php        # Kutengeneza folda mpya
├── preview.php              # Kushughulikia onyesho la faili
└── README.md
```

---

## 🎨 Kubadilisha Muonekano / Customization

Rangi na mandhari inaweza kubadilishwa kwenye [`assets/css/style.css`](assets/css/style.css):

```css
:root {
  --accent:      #6366f1;   /* Rangi kuu (indigo/purple) */
  --bg-primary:  #0f1117;   /* Asili ya ukurasa (dark) */
  --bg-card:     #1e2130;   /* Asili ya kadi */
}
```

Badilisha `--accent` peke yake kubadilisha rangi ya mfumo wote mara moja.

---

## 🌐 Lugha / Internationalization

Maneno yote ya UI yamo katika [`assets/js/main.js`](assets/js/main.js) ndani ya kitu `i18n`:

```js
const i18n = {
  en: { appTitle: 'TemboDrive', uploadTitle: 'Upload Files', ... },
  sw: { appTitle: 'TemboDrive', uploadTitle: 'Pakia Faili',  ... }
};
```

Kuongeza lugha mpya, ongeza ufunguo mpya (mfano `fr: { ... }`) na sasisha mantiki ya toggle.

---

## 🔒 Usalama / Security

- Majina ya faili yanasafishwa kabla ya kuhifadhi (`sanitizeFilename()`)
- Majina yanayorudiwa yanaongezwa nambari (hakuna overwrite)
- Matokeo yote yanakimbia `htmlspecialchars()`
- Kiwango cha juu cha faili: **1 GB kwa kila faili**

> **Ushauri:** Kwa mazingira ya uzalishaji, fikiria kuongeza HTTP Basic Auth kupitia `.htaccess` au ulinzi wa nywila wa cPanel kwenye saraka.

---

## 📜 Leseni / License

MIT License — huru kutumia, kubadilisha, na kusambaza.

---

<div align="center">

Imefanywa kwa ❤️ kwa Tanzania 🇹🇿

**TemboDrive** — *Nguvu ya Tembo, Wepesi wa Faili*

</div>
