---
name: Executive Precision
colors:
  surface: '#f8f9ff'
  surface-dim: '#ccdbf2'
  surface-bright: '#f8f9ff'
  surface-container-lowest: '#ffffff'
  surface-container-low: '#eef4ff'
  surface-container: '#e5efff'
  surface-container-high: '#dbe9ff'
  surface-container-highest: '#d4e4fa'
  on-surface: '#0d1c2d'
  on-surface-variant: '#45464d'
  inverse-surface: '#233143'
  inverse-on-surface: '#e9f1ff'
  outline: '#76777d'
  outline-variant: '#c6c6cd'
  surface-tint: '#565e74'
  primary: '#000000'
  on-primary: '#ffffff'
  primary-container: '#131b2e'
  on-primary-container: '#7c839b'
  inverse-primary: '#bec6e0'
  secondary: '#4648d4'
  on-secondary: '#ffffff'
  secondary-container: '#6063ee'
  on-secondary-container: '#fffbff'
  tertiary: '#000000'
  on-tertiary: '#ffffff'
  tertiary-container: '#0d1c2f'
  on-tertiary-container: '#76859b'
  error: '#ba1a1a'
  on-error: '#ffffff'
  error-container: '#ffdad6'
  on-error-container: '#93000a'
  primary-fixed: '#dae2fd'
  primary-fixed-dim: '#bec6e0'
  on-primary-fixed: '#131b2e'
  on-primary-fixed-variant: '#3f465c'
  secondary-fixed: '#e1e0ff'
  secondary-fixed-dim: '#c0c1ff'
  on-secondary-fixed: '#07006c'
  on-secondary-fixed-variant: '#2f2ebe'
  tertiary-fixed: '#d5e3fd'
  tertiary-fixed-dim: '#b9c7e0'
  on-tertiary-fixed: '#0d1c2f'
  on-tertiary-fixed-variant: '#3a485c'
  background: '#f8f9ff'
  on-background: '#0d1c2d'
  surface-variant: '#d4e4fa'
typography:
  h1:
    fontFamily: Manrope
    fontSize: 30px
    fontWeight: '700'
    lineHeight: 38px
    letterSpacing: -0.02em
  h2:
    fontFamily: Manrope
    fontSize: 24px
    fontWeight: '600'
    lineHeight: 32px
    letterSpacing: -0.01em
  h3:
    fontFamily: Manrope
    fontSize: 20px
    fontWeight: '600'
    lineHeight: 28px
  body-lg:
    fontFamily: Inter
    fontSize: 16px
    fontWeight: '400'
    lineHeight: 24px
  body-md:
    fontFamily: Inter
    fontSize: 14px
    fontWeight: '400'
    lineHeight: 20px
  body-sm:
    fontFamily: Inter
    fontSize: 12px
    fontWeight: '400'
    lineHeight: 16px
  label-md:
    fontFamily: Inter
    fontSize: 12px
    fontWeight: '600'
    lineHeight: 16px
    letterSpacing: 0.05em
  data-mono:
    fontFamily: Inter
    fontSize: 13px
    fontWeight: '500'
    lineHeight: 18px
rounded:
  sm: 0.125rem
  DEFAULT: 0.25rem
  md: 0.375rem
  lg: 0.5rem
  xl: 0.75rem
  full: 9999px
spacing:
  base: 4px
  xs: 8px
  sm: 12px
  md: 16px
  lg: 24px
  xl: 32px
  2xl: 48px
  container-margin: 24px
  gutter: 20px
---

## Brand & Style
The design system is engineered for high-density data environments where clarity and authority are paramount. It targets enterprise-level administrators and analysts who require a tool that feels reliable, sophisticated, and fast. 

The aesthetic follows a **Corporate / Modern** style with a focus on **Tonal Layering**. It avoids unnecessary ornamentation, instead using subtle transitions in grayscale and deep blues to establish hierarchy. The emotional response is one of "focused control"—reducing cognitive load through organized layouts and a calming, professional color story.

## Colors
This design system utilizes a "Deep Sea" palette to ground the interface in a professional tone.

*   **Primary (Deep Blue):** Used for sidebars, navigation headers, and high-level structural elements to provide a sense of stability.
*   **Secondary (Indigo Accent):** A vibrant Indigo reserved for primary actions, active states, and data highlights.
*   **Tertiary (Slate):** Used for secondary text and icons to create a soft hierarchy against the dark primary tones.
*   **Neutral (Cool Gray):** A range of grays for borders, disabled states, and background variations.

The interface primarily uses a light background for data density, but high-contrast dark sidebars are encouraged to frame the content area.

## Typography
The typography strategy maximizes legibility in complex views. 

**Manrope** is used for headlines to provide a modern, refined character. **Inter** is the workhorse for all body and tabular data due to its exceptional tall x-height and neutral tone. For data tables and numerical displays, utilize `data-mono` to ensure tabular numbers align vertically for easier scanning and comparison.

## Layout & Spacing
The design system employs a **Fluid Grid** with a 12-column structure for the main content area. 

*   **Sidebar:** Fixed width at 260px for desktop, collapsible to 64px (icon-only).
*   **Margins:** 24px outer margins for dashboard views to give the content "room to breathe."
*   **Vertical Rhythm:** An 8px-based spacing system is used for component internal padding, while a 4px-based system is used for micro-adjustments in data-heavy tables.

## Elevation & Depth
This design system uses **Tonal Layers** combined with **Low-contrast outlines** to define depth without visual clutter.

*   **Level 0 (Background):** A soft gray (`#F8FAFC`) serves as the canvas.
*   **Level 1 (Cards):** Pure white background with a 1px border (`#E2E8F0`) and a very soft, diffused shadow (0px 4px 6px -1px rgba(15, 23, 42, 0.05)).
*   **Level 2 (Dropdowns/Modals):** White background with a more pronounced shadow (0px 10px 15px -3px rgba(15, 23, 42, 0.1)) to indicate a higher z-index interaction.

## Shapes
The shape language is **Soft**, striking a balance between the rigidity of legacy enterprise software and the excessive roundness of consumer apps. 

Standard components (buttons, inputs) use a 4px (0.25rem) radius. Larger layout containers like cards and dashboard panels use an 8px (0.5rem) radius. This subtle rounding maintains a professional, geometric feel while appearing modern and accessible.

## Components

### Sidebars & Navigation
The sidebar is the primary navigation hub. It uses the **Primary (Deep Blue)** color as its background. Icons should be monochrome (Slate) with the active state indicated by a vertical Indigo bar on the left edge and white text.

### Data Tables
Tables are the heart of the dashboard. They must be borderless with horizontal dividers only. Row height should be set to 52px for standard viewing and 40px for "compact" mode. Use `data-mono` for all numerical values.

### Cards
Cards are used to group related widgets. Every card must have a consistent header with a `label-md` title and an optional action menu (three dots). Card backgrounds are white with Level 1 elevation.

### Buttons & Inputs
*   **Primary Button:** Indigo background, white text, 4px radius.
*   **Secondary Button:** Ghost style with Slate border and text.
*   **Input Fields:** 1px Slate-200 border, turning Indigo on focus. Labels sit 4px above the field in `body-sm` bold.

### Charts & Visualization
Use the Indigo accent as the primary data color. For secondary series, use a sequence of Slate-400, Teal-500, and Amber-400. Grid lines in charts should be kept to a minimum using the lightest gray (`#F1F5F9`).