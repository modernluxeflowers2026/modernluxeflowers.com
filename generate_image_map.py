from reportlab.lib.pagesizes import letter
from reportlab.lib.styles import getSampleStyleSheet, ParagraphStyle
from reportlab.lib.units import inch
from reportlab.lib import colors
from reportlab.platypus import SimpleDocTemplate, Paragraph, Spacer, Table, TableStyle, HRFlowable
from reportlab.lib.enums import TA_LEFT, TA_CENTER

OUTPUT = "/home/user/modernluxeflowers.com/MLF-Image-Map.pdf"

doc = SimpleDocTemplate(
    OUTPUT,
    pagesize=letter,
    leftMargin=0.75*inch,
    rightMargin=0.75*inch,
    topMargin=0.75*inch,
    bottomMargin=0.75*inch,
)

GOLD   = colors.HexColor("#c9a96e")
BLACK  = colors.HexColor("#0a0a0a")
DARK   = colors.HexColor("#1a1a17")
CREAM  = colors.HexColor("#f5f0e8")
MUTED  = colors.HexColor("#a89e90")
WHITE  = colors.white

styles = getSampleStyleSheet()

title_style = ParagraphStyle("title", fontName="Helvetica-Bold", fontSize=20,
    textColor=GOLD, spaceAfter=4, alignment=TA_CENTER)
subtitle_style = ParagraphStyle("subtitle", fontName="Helvetica", fontSize=10,
    textColor=MUTED, spaceAfter=2, alignment=TA_CENTER)
page_heading_style = ParagraphStyle("ph", fontName="Helvetica-Bold", fontSize=13,
    textColor=GOLD, spaceBefore=16, spaceAfter=6)
note_style = ParagraphStyle("note", fontName="Helvetica-Oblique", fontSize=8,
    textColor=MUTED, spaceAfter=8)
cell_num_style = ParagraphStyle("cn", fontName="Helvetica-Bold", fontSize=10, textColor=GOLD)
cell_label_style = ParagraphStyle("cl", fontName="Helvetica-Bold", fontSize=9, textColor=WHITE)
cell_file_style = ParagraphStyle("cf", fontName="Helvetica", fontSize=7, textColor=MUTED)

def shortname(src):
    if "zyrosite" in src:
        return "(Zyrosite CDN — logo/designer photo)"
    name = src.split("/")[-1]
    name = name.replace("%20", " ").replace("%28", "(").replace("%29", ")")
    if len(name) > 52:
        name = name[:49] + "..."
    return name

pages = [
    {
        "title": "Homepage",
        "file": "index.html",
        "images": [
            (1,  "Hero Background",         "images/chicago-luxury-arrangement-hero.jpeg"),
            (2,  "Designer Photo (About)",  "assets.zyrosite.com — img_4442 (designer)"),
            (3,  "Collections Grid #1 — Luxe Love",    "images/WhatsApp Image 2026-05-30 at 4.56.15 PM (3).jpeg"),
            (4,  "Collections Grid #2 — Boxful Art",   "images/WhatsApp Image 2026-05-30 at 4.56.15 PM (7).jpeg"),
            (5,  "Collections Grid #3 — Petals & Stems","images/WhatsApp Image 2026-05-30 at 4.56.14 PM (10).jpeg"),
        ]
    },
    {
        "title": "Corporate Floral Design",
        "file": "corporate-floral-design-chicago.html",
        "images": [
            (1,  "Hero Background",             "images/WhatsApp Image 2026-05-30 at 4.56.17 PM (3).jpeg"),
            (2,  "Bottom Panel — Left",         "images/WhatsApp Image 2026-05-30 at 4.56.14 PM (6).jpeg"),
            (3,  "Bottom Panel — Center",       "images/WhatsApp Image 2026-05-30 at 4.56.15 PM (3).jpeg"),
            (4,  "Bottom Panel — Right",        "images/WhatsApp Image 2026-05-30 at 4.56.14 PM (5).jpeg"),
        ]
    },
    {
        "title": "Weddings & Events",
        "file": "weddings-and-events-floral-chicago.html",
        "images": [
            (1,  "Wedding Trio — Left",         "images/WhatsApp Image 2026-05-30 at 4.56.13 PM (1).jpeg"),
            (2,  "Wedding Trio — Center",       "images/WhatsApp Image 2026-05-30 at 4.56.13 PM (2).jpeg"),
            (3,  "Wedding Trio — Right",        "images/WhatsApp Image 2026-05-30 at 4.56.14 PM (8).jpeg"),
            (4,  "Events Duo — Left",           "images/event-005.jpeg"),
            (5,  "Events Duo — Right",          "images/event-006.jpeg"),
        ]
    },
    {
        "title": "Luxe Love",
        "file": "luxe-love-unique-flower-arrangements.html",
        "images": [
            (1,  "Mosaic Hero (large, top left)",       "images/WhatsApp Image 2026-05-30 at 4.56.15 PM (3).jpeg"),
            (2,  "Bento Wide Top",                      "images/WhatsApp Image 2026-05-30 at 4.56.14 PM (6).jpeg"),
            (3,  "Bento Mid Left",                      "images/WhatsApp Image 2026-05-30 at 4.56.14 PM (5).jpeg"),
            (4,  "Bento Mid Right",                     "images/WhatsApp Image 2026-05-30 at 4.56.14 PM (9).jpeg"),
            (5,  "Mosaic Wide Break (full-width strip)", "images/WhatsApp Image 2026-05-30 at 4.56.17 PM (3).jpeg"),
            (6,  "Mosaic Bottom 1 — Left",              "images/WhatsApp Image 2026-05-30 at 4.56.16 PM (3).jpeg"),
            (7,  "Mosaic Bottom 2 — Center",            "images/WhatsApp Image 2026-05-30 at 4.56.14 PM (11).jpeg"),
            (8,  "Mosaic Bottom 3 — Right",             "images/WhatsApp Image 2026-05-30 at 4.56.14 PM (7).jpeg"),
        ]
    },
    {
        "title": "Boxful Art (Gift Giving)",
        "file": "gift-giving-boxful-art.html",
        "images": [
            (1,  "Gallery #1",  "images/WhatsApp Image 2026-05-30 at 4.56.15 PM (7).jpeg"),
            (2,  "Gallery #2",  "images/WhatsApp Image 2026-05-30 at 4.56.13 PM.jpeg"),
            (3,  "Gallery #3",  "images/WhatsApp Image 2026-05-30 at 4.56.14 PM (3).jpeg"),
            (4,  "Gallery #4",  "images/WhatsApp Image 2026-05-30 at 4.56.15 PM.jpeg"),
            (5,  "Gallery #5",  "images/WhatsApp Image 2026-05-30 at 4.56.15 PM (2).jpeg"),
            (6,  "Gallery #6",  "images/WhatsApp Image 2026-05-30 at 4.56.17 PM.jpeg"),
        ]
    },
    {
        "title": "Petals & Stems",
        "file": "petals-and-stems.html",
        "images": [
            (1,   "Gallery 1 — Mosaic Hero",        "images/WhatsApp Image 2026-05-30 at 4.56.14 PM (10).jpeg"),
            (2,   "Gallery 1 — Bento 1",            "images/lapel-wedding-grey.jpeg"),
            (3,   "Gallery 1 — Bento 2",            "images/WhatsApp Image 2026-05-30 at 4.56.14 PM (8).jpeg"),
            (4,   "Gallery 1 — Bento 3",            "images/WhatsApp Image 2026-05-30 at 4.56.13 PM (2).jpeg"),
            (5,   "Gallery 1 — Bento 4",            "images/WhatsApp Image 2026-05-30 at 4.56.13 PM (4).jpeg"),
            (6,   "Gallery 1 — Bottom 1",           "images/WhatsApp Image 2026-05-30 at 4.56.16 PM (1).jpeg"),
            (7,   "Gallery 1 — Bottom 2",           "images/WhatsApp Image 2026-05-30 at 4.56.16 PM.jpeg"),
            (8,   "Gallery 1 — Bottom 3",           "images/WhatsApp Image 2026-05-30 at 4.56.13 PM (1).jpeg"),
            (9,   "Gallery 2 — Mosaic Hero",        "images/WhatsApp Image 2026-05-30 at 4.56.15 PM (1).jpeg"),
            (10,  "Gallery 2 — Bento 1",            "images/WhatsApp Image 2026-05-30 at 4.56.15 PM (2).jpeg"),
            (11,  "Gallery 2 — Bento 2",            "images/WhatsApp Image 2026-05-30 at 4.56.15 PM (3).jpeg"),
            (12,  "Gallery 2 — Bento 3",            "images/WhatsApp Image 2026-05-30 at 4.56.15 PM (4).jpeg"),
            (13,  "Gallery 2 — Bento 4",            "images/WhatsApp Image 2026-05-30 at 4.56.15 PM (5).jpeg"),
            (14,  "Gallery 2 — Bottom 1",           "images/WhatsApp Image 2026-05-30 at 4.56.18 PM.jpeg"),
            (15,  "Gallery 2 — Bottom 2",           "images/WhatsApp Image 2026-05-30 at 4.56.14 PM (1).jpeg"),
            (16,  "Gallery 2 — Bottom 3",           "images/event-002.jpeg"),
        ]
    },
    {
        "title": "About Us",
        "file": "about-us-floral-design-business.html",
        "images": [
            (1,  "Portrait / Designer Photo",  "images/jo-and-erik.png"),
        ]
    },
    {
        "title": "Kind Words (Reviews)",
        "file": "client-reviews.html",
        "images": [
            (1,  "Featured Review Photo",  "images/marie-c-kindwords.jpg"),
        ]
    },
    {
        "title": "Contact",
        "file": "contact-us-floral-arrangements.html",
        "images": [
            (1,  "No content images",  "— (no swappable images on this page)"),
        ]
    },
]

story = []

# Title block
story.append(Spacer(1, 0.1*inch))
story.append(Paragraph("Modern LUXE Flowers", title_style))
story.append(Paragraph("Image Position Reference Map", subtitle_style))
story.append(Paragraph("Use image numbers when requesting photo swaps (e.g. \"Corporate #3\", \"Petals &amp; Stems #12\")", subtitle_style))
story.append(Spacer(1, 0.15*inch))
story.append(HRFlowable(width="100%", thickness=1, color=GOLD))
story.append(Spacer(1, 0.15*inch))

for page in pages:
    story.append(Paragraph(page["title"], page_heading_style))
    story.append(Paragraph(f"File: {page['file']}", note_style))

    table_data = [
        [
            Paragraph("#", cell_num_style),
            Paragraph("Position / Label", cell_label_style),
            Paragraph("Current Filename", cell_label_style),
        ]
    ]
    for num, label, filename in page["images"]:
        table_data.append([
            Paragraph(str(num), cell_num_style),
            Paragraph(label, ParagraphStyle("l", fontName="Helvetica", fontSize=9, textColor=CREAM)),
            Paragraph(filename, ParagraphStyle("f", fontName="Helvetica", fontSize=7.5, textColor=MUTED)),
        ])

    t = Table(table_data, colWidths=[0.4*inch, 2.6*inch, 3.8*inch])
    t.setStyle(TableStyle([
        ("BACKGROUND",    (0, 0), (-1, 0),  DARK),
        ("ROWBACKGROUNDS",(0, 1), (-1, -1), [colors.HexColor("#111111"), colors.HexColor("#161613")]),
        ("TEXTCOLOR",     (0, 0), (-1, 0),  GOLD),
        ("FONTNAME",      (0, 0), (-1, 0),  "Helvetica-Bold"),
        ("FONTSIZE",      (0, 0), (-1, 0),  9),
        ("TOPPADDING",    (0, 0), (-1, -1), 6),
        ("BOTTOMPADDING", (0, 0), (-1, -1), 6),
        ("LEFTPADDING",   (0, 0), (-1, -1), 8),
        ("RIGHTPADDING",  (0, 0), (-1, -1), 8),
        ("GRID",          (0, 0), (-1, -1), 0.5, colors.HexColor("#2a2820")),
        ("VALIGN",        (0, 0), (-1, -1), "MIDDLE"),
    ]))
    story.append(t)
    story.append(Spacer(1, 0.1*inch))

doc.build(story)
print(f"PDF written to {OUTPUT}")
