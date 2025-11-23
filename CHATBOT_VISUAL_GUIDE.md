# 🎨 Floating Chatbot Widget - Visual Reference Guide

## 📱 Desktop View

```
┌────────────────────────────────────────────────────────┐
│ University Rental Equipment Dashboard                  │
├────────────────────────────────────────────────────────┤
│                                                        │
│  Welcome back, Student!                               │
│  _________________________________________             │
│                                                   ╭────╮
│  ┌─────────┐  ┌─────────┐  ┌─────────┐        │ 💬 │
│  │ Add     │  │ My      │  │ AI      │        │    │
│  │Booking  │  │Bookings │  │Asst.    │        │ AI │
│  └─────────┘  └─────────┘  └─────────┘        │Asst│
│                                               │ 1  │
│  Quick Stats:                                 ╰────╯
│  • Equipment: 45
│  • Bookings: 12
│  • Available: 38                         ╭──────────╮
│                                         │ AI Asst  │X│
│                                         ├──────────┤
│                                         │ Hello!   │
│                                         │ How can  │
│                                         │ I help?  │
│                                         │          │
│                                         │ [input]  │
│                                         │ [Send]   │
│                                         ╰──────────╯
│
└────────────────────────────────────────────────────────┘
```

---

## 📲 Mobile View

```
┌──────────────────────┐
│ University Rental... │
├──────────────────────┤
│ Floating button      │
│ (top of viewport)    │
│      ╭────╮          │
│      │ 💬 │          │
│ When clicked, shows: │
│ ╭──────────────────╮ │
│ │ AI Assistant  X  │ │
│ ├──────────────────┤ │
│ │ Hello! How can   │ │
│ │ I help you?      │ │
│ │                  │ │
│ │ [Input field]    │ │
│ │ [Send Button]    │ │
│ │                  │ │
│ │ [Type here]  [>] │ │
│ ╰──────────────────╯ │
│                      │
└──────────────────────┘

(Full-screen chat on mobile)
```

---

## 🎨 Color Scheme

### Gradient Colors
```
User Messages:     Bot Messages:
┌──────────────┐  ┌──────────────┐
│ #667eea      │  │ #f3f4f6      │
│     ↓        │  │ (Light Gray) │
│ #764ba2      │  │              │
│ (Blue→Purple)│  │ Dark Text    │
└──────────────┘  └──────────────┘
```

### Supporting Colors
- Background: White
- Input Area: White with subtle border
- Text (Bot): #374151 (Dark Gray)
- Text (User): White
- Timestamp: #999999
- Border: #e5e7eb (Light Gray)

---

## 🎯 Widget Dimensions

### Desktop Layout
```
┌────────────────────────────────────────────┐
│                                            │
│                                  ↓ 24px    │
│                        ╭─────────────────╮ │
│                        │   Chat Window   │ │
│                        │  360px × 500px  │ │
│                        │   ┌─────────┐  │ │
│                    56px│  💬│ AI Asst │X│  │
│                   ↓    │   └─────────┘  │ │
│                  ╱ ╲   │   Messages...  │ │
│                ╱     ╲ │   [Input]  [>] │ │
│                       ╰─────────────────╯ │
│                        ↑ 24px             │
│                        ↑ from right       │
└────────────────────────────────────────────┘
```

### Mobile Layout
```
┌──────────────────────┐
│      Chat Full Width │
│      ┌────────────┐  │
│      │ AI Asst │X│  │
│      ├────────────┤  │
│      │ Messages   │  │
│      │ ...        │  │
│      │ ...        │  │
│      │ [Input] [>]  │
│      └────────────┘  │
│   (100% width)       │
└──────────────────────┘
```

---

## 🎬 Animation Sequence

### Opening Widget (slideUp animation)
```
Frame 1: Widget closed
         ╭────╮
         │ 💬 │  ← Toggle button visible
         ╰────╯

Frame 2: Sliding up
         ╭──────────╮
         │ AI Asst  │  ← Window sliding up
         │ ...      │
         │ [Input]  │
         ╰────────────╯ ← 50% visible
         ╭────╮
         │ 💬 │

Frame 3: Fully open
         ╭──────────╮
         │ AI Asst  │  ← Window fully visible
         │ ...      │
         │ [Input]  │
         ╰──────────╯
         
         (Toggle button hidden)
```

### Message Animation (messageSlide)
```
Text appears: "What equipment is available?"

┌────────────────────────────────┐
│ Animated in from right:        │
│ • Slides from 50px offset      │
│ • Opacity: 0 → 1               │
│ • Duration: 300ms              │
│ • Easing: ease-out             │
│                                │
│          ┌──────────────┐       │
│          │ What equip...│ ← Slide│
│          └──────────────┘       │
└────────────────────────────────┘
```

### Typing Indicator (3-dot bounce)
```
Time:  0ms    200ms   400ms   600ms   800ms
       ↓      ↓       ↓       ↓       ↓
Dot 1: • → ↑ → · → · → · → ↑ → · → · (repeat)
Dot 2: · → · → • → ↑ → · → · → · → ↑ (repeat)
Dot 3: · → · → · → · → • → ↑ → · → · (repeat)

Visual:
● ○ ○   ○ ● ○   ○ ○ ●   ○ ○ ○   repeat...
```

### Unread Badge (pulse animation)
```
Size:    100%  ↓ 90%  ↓ 80%  ↑ 90%  ↑ 100%  
Opacity: 1.0  → 0.8 → 0.6 → 0.8 → 1.0 (repeat)

Visual:
     ⊙  →  ⊕  →  ⊙  (pulsing circle with "1")
```

---

## 🎨 CSS Classes & Their Purpose

### Structure Classes
```
.chatbot-widget-container    ← Main container (fixed position)
├── .chatbot-toggle-btn      ← Floating button (56x56px)
│   ├── <i class="fas fa-comments">  ← Chat icon
│   └── .unread-badge        ← Red notification badge
│
└── .chatbot-window          ← Chat window (360x500px)
    ├── .chatbot-header      ← Header section
    │   ├── .chatbot-header-info
    │   │   ├── .chatbot-title      ← "AI Assistant"
    │   │   └── .chatbot-subtitle   ← "UniEquip Support"
    │   └── .chatbot-close-btn      ← X button
    │
    ├── .chatbot-messages    ← Messages container (scrollable)
    │   ├── .chatbot-message.bot-message
    │   │   ├── .message-content    ← Gray bubble (bot)
    │   │   └── .message-time       ← Timestamp
    │   │
    │   └── .chatbot-message.user-message
    │       ├── .message-content    ← Blue bubble (user)
    │       └── .message-time       ← Timestamp
    │
    └── .chatbot-input-area  ← Input section
        └── form#chatbotForm
            ├── input#chatbotInput  ← Text input
            └── .chatbot-send-btn   ← Send button
```

---

## 🔄 Interaction Flow

### User Opens Widget
```
Click floating button
    ↓
JavaScript: openChatbot()
    ↓
Window display: none → flex
    ↓
Trigger slideUp animation
    ↓
Focus input field
    ↓
Hide unread badge
    ↓
Window visible to user
```

### User Sends Message
```
Type message in input
    ↓
Press Enter or click Send
    ↓
Form submit event fires
    ↓
sendChatbotMessage() function
    ├─ Get message text
    ├─ Add to messages display (right side, blue)
    ├─ Clear input field
    ├─ Show typing indicator
    ├─ Send to ai_openrouter_api.php (fetch POST)
    ├─ Wait for response
    ├─ Remove typing indicator
    ├─ Add response to display (left side, gray)
    └─ Auto-scroll to bottom
```

### Message Display
```
Every message shows:
┌────────────────────┐
│ Message text here  │
├────────────────────┤
│ 12:34 PM           │  ← Timestamp
└────────────────────┘

User messages: Right side, Blue gradient, 12:34 AM
Bot messages:  Left side, Gray background, 12:35 AM
```

---

## 🎯 State Management

### Widget States
```
CLOSED:
- Window: display: none
- Button: visible, floating
- Badge: may show if unread
- Opacity: 100%

OPENING:
- Animation: slideUp 300ms
- Opacity: 0 → 100%
- Position: bottom slides up

OPEN:
- Window: display: flex
- Messages: visible, scrollable
- Input: focused
- Button: hidden behind window

CLOSING:
- Animation: slideDown 300ms
- Opacity: 100% → 0
- Button: becomes visible again
```

### Unread Badge States
```
HIDDEN (normal):
- display: none
- Badge invisible

SHOWING (new message while closed):
- display: flex
- Pulse animation active
- Number: "1" visible

HIDDEN (on open):
- display: none
- Animation stops
```

---

## 📐 Responsive Breakpoints

### Desktop (> 480px)
```
┌──────────────────────────────────────┐
│          Dashboard Area              │
│                                ╭─────╮
│  Content                       │ Chat│
│                                │ 360x│
│                                │ 500 │
│                                │  px │
│                                ╰─────╯
│                              24px from corner
└──────────────────────────────────────┘

Chat Window: 360px wide × 500px tall
Position: bottom-right with 24px margin
Animation: Smooth slideUp
```

### Mobile (≤ 480px)
```
┌────────────────────┐
│  Dashboard         │
├────────────────────┤
│ ╭────────────────╮ │
│ │ AI Asst      X │ │  Full-screen
│ ├────────────────┤ │  chat
│ │ Messages...    │ │
│ │ ...            │ │
│ │ [Input]  [>]   │ │
│ ╰────────────────╯ │
│                    │
└────────────────────┘

Chat Window: 100% width × 100% height
Position: Full screen
Floating button: Hidden (would cover chat)
```

---

## 🎨 Theme Customization Examples

### Dark Mode Ready
To implement dark mode, modify these colors:
```css
/* Change bot message background */
.bot-message .message-content {
    background: #2d3748;    /* Dark gray */
    color: #e2e8f0;         /* Light text */
}

/* Change chat window background */
.chatbot-window {
    background: #1a202c;    /* Very dark */
    color: #e2e8f0;
}
```

### Custom Gradient
To change the gradient color:
```css
/* Current */
.user-message .message-content {
    background: linear-gradient(135deg, #667eea, #764ba2);
}

/* Alternative examples */
/* Red theme */
background: linear-gradient(135deg, #f093fb, #f5576c);

/* Green theme */
background: linear-gradient(135deg, #4facfe, #00f2fe);

/* Orange theme */
background: linear-gradient(135deg, #fa709a, #fee140);
```

---

## 📋 Browser Compatibility

### Supported Browsers
- ✅ Chrome 90+
- ✅ Firefox 88+
- ✅ Safari 14+
- ✅ Edge 90+
- ✅ Mobile browsers (Chrome, Safari, Firefox)

### Features by Browser
```
CSS Grid Layout      ✅ All modern browsers
CSS Flexbox          ✅ All modern browsers
CSS Animations       ✅ All modern browsers
Fetch API            ✅ All modern browsers
Template Literals    ✅ All modern browsers
Session Storage      ✅ All modern browsers
```

---

## 🎊 Summary

The floating chatbot widget provides:
- Modern, professional appearance
- Smooth, polished animations
- Responsive design for all devices
- Easy customization
- Accessibility features
- Security (HTML escaping)
- Error handling

Perfect for enhancing student support experience! ✨
