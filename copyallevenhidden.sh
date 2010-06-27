sudo find . -depth -print0 | sudo cpio --null --sparse -pvd /media/1TB/cache/
