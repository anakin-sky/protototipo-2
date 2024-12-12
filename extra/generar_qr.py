import base64
import sys
import qrcode
from io import BytesIO

data = ';'.join(sys.argv[1:])

qr = qrcode.QRCode(
    version=1,
    error_correction=qrcode.constants.ERROR_CORRECT_L,
    box_size=10,
    border=4,
)
qr.add_data(data)
qr.make(fit=True)

img = qr.make_image(fill_color="black", back_color="white")

buffer = BytesIO()
img.save(buffer, format="PNG")
img_str = buffer.getvalue()
print(base64.b64encode(img_str).decode())


