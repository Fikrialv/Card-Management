# RFID Card Management

Domain mengelola pengajuan customer, review manual, kartu RFID, pergerakan stok, Catatan Pengadaan, dan histori yang dapat diaudit.

## Language

**Customer Request**: Pengajuan eksternal dengan nama institusi, CRM, tipe, tanggal permintaan, Pengajuan Kartu Excel, dan Bukti Bayar. Tidak memuat Request Item inline.
_Avoid_: Order, tiket

**Request Number**: Identitas publik Customer Request yang dipasangkan dengan Tracking Code untuk tracking.
_Avoid_: Tracking number

**Tracking Code**: Credential rahasia customer untuk membuka proyeksi tracking aman ketika dipasangkan dengan Request Number.
_Avoid_: Token admin

**Priority Queue**: Urutan review server-side berdasarkan overdue, due today, urgency, SLA/deadline, request type, dan waktu dibuat; tidak mengubah status request.
_Avoid_: Auto-approval

**Priority Override**: Priority manual beralasan yang menggantikan hasil kalkulasi sampai dicabut.
_Avoid_: Approval override

**RFID Card**: Nomor kartu unik yang dapat tersedia, dialokasikan, atau keluar dari inventori.
_Avoid_: Stok

**RFID Range**: Rentang inklusif nomor RFID berurutan yang masuk inventori.
_Avoid_: Batch bila tidak berurutan

**Stock Movement**: Catatan immutable perubahan inventori berupa Stock In atau Stock Out.
_Avoid_: Edit stok

**Catatan Pengadaan**: Catatan admin untuk tanggal pengajuan, PIC, QTY RFID diajukan, dan note; dapat diterima sekali dengan tanggal/QTY/range RFID yang kemudian melakukan Stock In atomik.
_Avoid_: Stock Request, pengiriman

**Customer Notification**: Event customer yang hanya dapat dibaca melalui pasangan Request Number dan Tracking Code; in-app dulu, Web Push opsional setelah HTTPS/VAPID siap.
_Avoid_: Broadcast publik

**Requested Quantity**: Jumlah kartu RFID yang diminta oleh satu Customer Request; setiap pengajuan Kartu Baru memiliki jumlahnya sendiri dan quota mengalokasikan jumlah ini saat approval.
_Avoid_: Mengambil jumlah dari Request Item atau stok saat submit

**Payment Proof**: Bukti bayar wajib untuk setiap Customer Request; quota hanya menentukan pembagian free/paid saat approval.
_Avoid_: Bukti bayar conditional berdasarkan quota
