# UploadX #

## What is UploadX?
To put it simply, UploadX is software built for ShareX to allow you to upload files and images to your own server and website.

If you're like me, you like legal security. When you upload an image to, say, Imgur, your image now belongs to Imgur. The server your image is on, is not yours. You may have some control over the image, such as deleting it, but overall, Imgur has full control of it. They can delete it, remove it, use access logs to track you down, etc. UploadX removes this problem, because it's your site. Your server. Your files. There is no un-known administrator, no company that has to respond to DCMA requests or a subpoena. You're the only one who has control. (for the most part, your server host can still shut you down if they want to)

## How does it work?
If you're using this software, you're using ShareX.

It works by setting up ShareX to upload to a custom host (your server). Whenever you take a screenshot, upload a file, or record a video in ShareX, it will get sent to your own server, processed by UploadX, and then a small URL will be sent back to you. It's easy, it's quick, it's simple, and, it's yours.

## How do I install it?

SSH into your box.

Clone the repository `git clone https://github.com/UploadX/UploadX/`

Read the [quick start guide.](https://github.com/UploadX/UploadX/blob/master/docs/quickstart.md)

Follow the appropriate guide for [Apache](https://github.com/UploadX/UploadX/blob/master/docs/apache.md) or [nginx](https://github.com/UploadX/UploadX/blob/master/docs/nginx.md).

## How do I configure it?

Once UploadX is installed, go to the admin panel, located at `http://yoursite/<whatever you called it>/admin/` (don't forget the end / !)

Put in the default password (password)

Go to the "settings" panel, choose your general settings, theme, and most notably, password.

Go to the users panel and create a user. The user name here will show up in the side panel if you enable uploader view.

## How do I use it with ShareX?
[Insert link to setup wiki page here]
