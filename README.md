# Multilingual Monopage Curriculum Vitae

A highly customizable, responsive, and multilingual Curriculum Vitae (CV) / Resume web application. Built with **Vue 3** and the **Quasar Framework**, it provides a stunning print-friendly public profile page and a password-secured administration panel to edit and translate all sections in real-time.

---

## 🚀 Key Features

* **Multi-Language Support**: Complete translations for 8 built-in languages (Italian 🇮🇹, English 🇬🇧, French 🇫🇷, German 🇩🇪, Spanish 🇪🇸, Russian 🇷🇺, Japanese 🇯🇵, Chinese 🇨🇳).
* **Browser Language Detection**: Automatically detects browser's language on initial load, falling back to English or the first available CV language.
* **Secured Admin Panel**: Login lock screen with custom back-to-CV action. Contains panel tabs for each profile component.
* **Database Backup & Restore**: Download and import the entire CV configuration directly from the administrative Settings panel.
* **Dynamic Styling Themes**: Real-time theme engine switcher featuring 10 professional color palettes (Default Quasar Blue, Navy & Charcoal, Forest & Sand, Burgundy & Gold, Teal & Slate, Dark Slate & Orange, Pink, Magenta, Darcula with ochre highlight, and Vaporwave) with visual color block previews in dropdown selectors.
* **Default Language Override**: Select and persist a specific default language for the public CV page, featuring a clearable setting that defaults back to browser detection.
* **Click-to-Dismiss Notifications**: Quick and simple global toast dismissal by clicking anywhere directly on the notification body.
* **Centralized Core Metadata**: Profile photo, name, and date of birth are saved globally at the database root to avoid translations redundancy.
* **Flexible List Reordering**: Accordion sections with move-up/down sorting controls, visibility toggling, and complete deletion with modal checks.
* **Translation Utilities**: Import existing profiles when translating your CV into a new language or start from a blank slate.
* **Export and Share**: Export your CV directly as a high-quality PDF or share it with one-click clipboard copying.
* **Interactive Project Footer**: Reusable layout referencing metadata from `package.json`, completely hidden during print or PDF generation.
* **Responsive Layouts**: Redesigned dashboard column grid widths (`col-md-2` for sidebar tabs, `col-md-10` for main panels) and optimized mobile column flex layouts.

---

## 🛠️ Installation & Getting Started

### Prerequisites
Make sure you have [Node.js](https://nodejs.org/) installed (version `>= 16.x` recommended) along with `npm`.

### 1. Install Dependencies
Run the following command in the project root to install the Quasar CLI and required dependencies:
```bash
npm install
```

### 2. Start Local Development Server
Launch the application locally in hot-reload mode:
```bash
npm run dev
# or: quasar dev
```
The app will be accessible at `http://localhost:9000/`.

### 3. Build for Production
Compile the project to generate a optimized production bundle inside the `dist/` directory:
```bash
npm run build
# or: quasar build
```

---

## 🔐 Administrative & Security Guide

The admin panel is accessible at `http://localhost:9000/#/admin`.

### 1. Lock Screen & First Login
* Upon opening the admin page, you will be greeted by a security lock screen asking for your password.
* **Default Password**: If no password has been configured yet, you can access the dashboard using `admin`.
* **Language Select**: The lock card features a language selector dropdown so you can change the admin interface language before logging in. On mobile, it displays only the flag emoji to conserve screen real estate.
* **Back to CV**: Quick action button to go back to the CV profile page.

### 2. Managing Your Settings & Backup
1. Navigate to the ⚙️ **Settings** tab in the admin drawer (marked with a cog icon).
2. **Password**: Enter your current password, type a new password, confirm it, and click **Change Password** to update the panel credentials.
3. **Theme**: Swap styling presets in real-time. Dropdown options display block previews of theme primary, secondary, and accent colors.
4. **Default Language**: Define the default language to serve to public visitors. Leaving this field clear will instruct the app to detect and match the browser language automatically on load.
5. **Backup**: Use the **Download Settings** button to save the entire `cv.json` database locally, or the **Import Settings** button to upload and apply an existing config file.

### 3. Curriculum Section Tabs
The Admin Panel features 10 dedicated sections:
* ⚙️ **Settings**: Lock credentials, passwords, default language, themes, and backup management.
* 👤 **Personal Info**: Full name, qualifications, profile image (avatar upload centered at the top of the tab), professional profile (about text), and custom contact fields (Email, Phone, links, etc.) with manual re-ordering.
* 💼 **Experience**: Chronological work experiences. Each item has drag-and-drop/arrows reordering, visibility switches, and list detail items.
* 🛠️ **Skills**: Tag chips with descriptions and documentation links. Backed by Sass variables `$chip-bg` and `$chip-text`.
* 🌐 **Languages**: General language proficiencies (Native, Conversational, etc.) with custom flag code support.
* 🎓 **Education**: School details, degrees, and grades.
* 📜 **Certificates**: Professional training courses, certifications, and Smau panels.
* 👥 **Other Skills**: Management and Communication skills.
* 💻 **Digital Skills**: Tech areas and projects (supports descriptions, links, and sorting).
* 📝 **Privacy**: Declarations and legal treatment of personal data.

### 4. Language Management in Dashboard
* **Dynamic Editing Switcher**: Change the editing language via the dropdown select in the top header.
* **Copy/Import System**: Selecting a language with no data prompts a dialog allowing you to **copy/import** data from an existing language CV profile, or **start with an empty** template. When importing, headers are automatically translated to the target language.
* **Delete Language**: Click the rectangular delete icon in the header (aligned next to the dropdown on desktop and wrapping on mobile) to remove a translation. The system will restrict deletion if there is only one active language remaining.

---

## 📁 Database Schema (`public/cv.json`)

All CV contents are stored in `public/cv.json`. Below is the structural overview:

```json
{
  "name": "John Doe",
  "img": "/avatar.jpg",
  "birthDate": "January 1, 1990",
  "theme": "default",
  "defaultLanguage": null,
  "languages": ["en", "it"],
  "data": {
    "en": {
      "info": {
        "icon": "mdi-account",
        "description": "Full-Stack Developer",
        "about": "...",
        "data": [
          { "label": "Email", "value": "email@example.com", "link": "mailto:email@example.com" }
        ]
      },
      "exp": {
        "icon": "mdi-briefcase",
        "title": "Experience",
        "data": [...]
      },
      "skill": { "icon": "mdi-tools", "title": "Skills", "data": [...] },
      "lang": { "icon": "mdi-translate", "title": "Languages", "data": [...] },
      "school": { "icon": "mdi-school", "title": "Education", "data": [...] },
      "courses": { "icon": "mdi-teach", "title": "Certificates", "data": [...] },
      "managementSkill": { "icon": "mdi-account-group", "title": "Management Skills", "data": [...] },
      "comunicativeSkill": { "icon": "mdi-account-voice", "title": "Communication Skills", "data": [...] },
      "digitalSkill": { "icon": "mdi-laptop", "title": "Digital Skills", "data": [...] },
      "others": { "icon": "mdi-information", "title": "Additional Information", "data": [...] },
      "privacy": { "icon": "mdi-draw", "title": "Privacy", "description": "..." }
    },
    "it": {
      "info": { ... },
      "exp": { ... }
    }
  },
  "adminPassword": "hashed-sha256-password-string"
}
```

* Ensure that `cv.json` has read/write permissions on your hosting environment to support direct HTTP saving via administrative panel APIs.
