# Design System Document

## 1. Overview & Creative North Star: "The Professional Sanctuary"
The HRIS landscape is traditionally cluttered and anxiety-inducing. This design system rejects the "data-heavy spreadsheet" aesthetic in favor of **The Professional Sanctuary**. Our Creative North Star focuses on clarity through abundance—specifically, the abundance of white space, tonal depth, and editorial-grade typography. 

We break the "standard app" template by utilizing **Intentional Asymmetry**. Instead of centering every element, we use left-aligned, oversized headers and staggered card layouts to create a rhythmic flow that guides the eye. The interface shouldn't feel like a tool; it should feel like a premium digital workspace that breathes.

## 2. Colors & Tonal Architecture
The palette is a sophisticated interplay of deep architectural blues and "oxygenated" neutrals. We move beyond flat UI by utilizing a strict hierarchy of light and shadow.

### The "No-Line" Rule
**Borders are prohibited for sectioning.** To define boundaries, you must use background color shifts. For example, a `surface-container-low` section should sit directly on a `surface` background. If you feel the need for a line, you haven't used your surface tiers effectively.

### Surface Hierarchy & Nesting
Treat the UI as a series of physical layers. Use the `surface-container` tiers to create "nested" depth:
*   **Base Layer:** `surface` (#faf8ff) or `surface-container-lowest` (#ffffff).
*   **Content Areas:** Use `surface-container-low` (#f2f3fe) to block out major sections like "Upcoming Holidays" or "Time-Off Balances."
*   **Interaction Points:** Use `surface-container-high` (#e7e7f2) for interactive elements that need to feel "elevated" above the content.

### The "Glass & Gradient" Rule
To elevate the HRIS from "utility" to "premium," use Glassmorphism for floating navigation bars or modal overlays. Apply `surface-container-lowest` with a 70% opacity and a 20px backdrop blur. 
*   **Signature Gradients:** For primary CTAs (like "Submit Request"), use a subtle linear gradient from `primary` (#0040a1) to `primary_container` (#0056d2) at a 135-degree angle. This adds a "lithographic" soul to the interface.

## 3. Typography: The Editorial Voice
We use a dual-typeface system to balance authority with approachability.

*   **Display & Headlines (Manrope):** This geometric sans-serif provides a modern, architectural feel. Use `display-md` for high-impact data points (e.g., "14 Days Remaining") and `headline-sm` for section titles. The wider apertures of Manrope ensure that even at large scales, the brand feels "open."
*   **Body & Labels (Inter):** Inter is our workhorse. It is engineered for readability on mobile screens. Use `body-md` for all standard descriptions and `label-md` for metadata. 
*   **Intentional Contrast:** Always pair a `headline-sm` (Manrope, Bold) with `body-sm` (Inter, Regular) to create a clear "Editorial Anchor" for every module.

## 4. Elevation & Depth
Depth is achieved through **Tonal Layering**, not structural scaffolding.

*   **The Layering Principle:** Instead of a shadow, place a `surface-container-lowest` card on top of a `surface-container` background. The subtle shift from #ffffff to #ededf8 creates a natural, soft lift.
*   **Ambient Shadows:** For high-level floating elements (e.g., a "Clock In" FAB), use an extra-diffused shadow: `offset: 0, 8px; blur: 24px; color: rgba(25, 27, 35, 0.06)`. This mimics natural light.
*   **The "Ghost Border" Fallback:** If accessibility requires a container boundary, use the `outline-variant` token (#c3c6d6) at **15% opacity**. This creates a "suggestion" of a border without breaking the fluid aesthetic.

## 5. Components

### Buttons
*   **Primary:** High-gloss gradient (`primary` to `primary-container`). `xl` roundedness (0.75rem). Use `on-primary` for text.
*   **Secondary:** No background. Use `primary` text with a `surface-container-high` background on hover/press.
*   **Tertiary:** `body-md` text in `primary` color, no container.

### Input Fields & Search
*   **The "Quiet" Input:** Abandon the 4-sided box. Use a `surface-container-low` background with a `xl` corner radius. The label (`label-md`) should sit 8px above the input area in `on-surface-variant`.
*   **Error States:** Use `error` (#ba1a1a) for the text and a subtle `error_container` (#ffdad6) for the background fill.

### Cards & Lists
*   **Card Anatomy:** Use `surface-container-lowest` for the card body. **Never use dividers.** To separate "Salary" from "Role" within a card, use 16px of vertical whitespace or a 2px vertical "accent" line in `primary_fixed` on the far left.
*   **The "Smart" List:** List items should have generous padding (20px vertical). Use `surface-variant` for leading icons to keep them from competing with the text.

### HRIS Specific: The "Status Micro-Chip"
*   Use `secondary_container` for neutral statuses (e.g., "Pending"). 
*   Use `tertiary_container` (#a93802) for urgent items (e.g., "Action Required"). These chips should have `full` roundedness (9999px) and use `label-sm` for typography.

## 6. Do’s and Don’ts

### Do
*   **Do** use asymmetrical margins. A 24px left margin and 16px right margin can make a dashboard feel like a custom-designed magazine.
*   **Do** use `primary_fixed_dim` for "soft" buttons that shouldn't distract from the main action.
*   **Do** embrace white space. If a screen feels "empty," it’s likely working perfectly.

### Don’t
*   **Don't** use pure black (#000000). Always use `on-surface` (#191b23) for text to maintain the premium, navy-tinted depth.
*   **Don't** use 1px dividers to separate list items. Use tonal shifts or 24px spacing.
*   **Don't** use standard "Material Blue." Use the specific `primary` (#0040a1) which has a deeper, more professional "Midnight" undertone.