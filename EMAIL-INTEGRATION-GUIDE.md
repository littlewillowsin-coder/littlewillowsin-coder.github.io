# Email Form Integration Guide
## Little Willows Academy Contact Form

This guide provides multiple options for integrating the contact form with email delivery.

---

## Option 1: Formspree (Recommended - Easiest)

### Setup Steps:
1. Go to https://formspree.io/
2. Sign up for a free account
3. Create a new form
4. Copy your form endpoint (looks like: `https://formspree.io/f/xyzabc123`)
5. In `contact.html`, find this line:
   ```html
   <form class="contact-form" action="https://formspree.io/f/YOUR_FORM_ID" method="POST">
   ```
6. Replace `YOUR_FORM_ID` with your actual Formspree form ID

### Features:
- ✅ No coding required
- ✅ Spam protection included
- ✅ Free for up to 50 submissions/month
- ✅ Email notifications
- ✅ Works immediately after setup

### Pricing:
- Free: 50 submissions/month
- Basic: $10/month - 1000 submissions
- Pro: Custom pricing

---

## Option 2: PHP Backend (Self-Hosted)

### Requirements:
- Web hosting with PHP support
- Email configured on your server

### Setup Steps:

1. **Create `send_email.php` file** (included in this package)

2. **Update contact.html form action:**
   ```html
   <form class="contact-form" action="send_email.php" method="POST">
   ```

3. **Configure email settings in send_email.php:**
   - Set `$to_email` to info@littlewillows.in
   - Customize email subject and message format

4. **Upload to your web server:**
   - Upload all HTML files
   - Upload send_email.php
   - Test the form

### Benefits:
- ✅ Complete control
- ✅ No monthly fees
- ✅ Unlimited submissions
- ✅ Can customize completely

### Note:
Most shared hosting providers support PHP email sending. If you experience issues, contact your hosting provider to enable mail() function.

---

## Option 3: EmailJS (JavaScript-Based)

### Setup Steps:
1. Sign up at https://www.emailjs.com/
2. Create an email service (Gmail, Outlook, etc.)
3. Create an email template
4. Add this script to your contact.html before `</body>`:

```html
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/@emailjs/browser@3/dist/email.min.js"></script>
<script type="text/javascript">
  (function(){
    emailjs.init("YOUR_PUBLIC_KEY");
  })();
</script>
<script>
  document.querySelector('.contact-form').addEventListener('submit', function(e) {
    e.preventDefault();
    
    emailjs.sendForm('YOUR_SERVICE_ID', 'YOUR_TEMPLATE_ID', this)
      .then(function() {
        alert('Thank you! We will contact you soon.');
        document.querySelector('.contact-form').reset();
      }, function(error) {
        alert('Sorry, there was an error. Please try again or call us directly.');
      });
  });
</script>
```

### Features:
- ✅ Works from static websites
- ✅ No backend required
- ✅ Free for 200 emails/month
- ✅ Multiple email service support

---

## Option 4: Google Forms Redirect

### Simple Alternative:
1. Create a Google Form with matching fields
2. Get the form's URL
3. Update contact.html form to redirect to Google Form
4. Or embed Google Form directly in an iframe

### Pros:
- ✅ Free forever
- ✅ Responses saved in Google Sheets
- ✅ Email notifications via Google
- ✅ Zero maintenance

### Cons:
- ❌ Less professional appearance
- ❌ Redirects away from your site

---

## Testing Your Form

After setup, test the form:

1. Fill out all required fields
2. Submit the form
3. Check if you receive the email at info@littlewillows.in
4. Verify all form fields appear in the email
5. Test from different devices (desktop, mobile)
6. Check spam folder if email doesn't arrive

---

## Troubleshooting

### Form not sending:
- Check form action URL is correct
- Verify email server is configured
- Check browser console for JavaScript errors
- Test with a different email address

### Emails going to spam:
- Add SPF and DKIM records to your domain
- Use authenticated email service
- Avoid spam trigger words in form
- Configure proper email headers

### Not receiving emails:
- Check spam/junk folder
- Verify info@littlewillows.in is active
- Test email server separately
- Check hosting provider email limits

---

## Security Considerations

1. **Add CAPTCHA:** Prevent spam submissions
   - Use Google reCAPTCHA v3 (invisible)
   - Or hCaptcha for GDPR compliance

2. **Input Validation:** Already included in HTML
   - Required fields marked
   - Email format validation
   - Character limits

3. **Rate Limiting:** Prevent abuse
   - Use Formspree's built-in protection
   - Or implement PHP rate limiting

4. **Sanitize Inputs:** In PHP backend
   - Strip HTML tags
   - Validate email format
   - Escape special characters

---

## Recommended Setup for Little Willows

**For Quick Launch:** Use Formspree (Option 1)
- Takes 5 minutes to setup
- Professional and reliable
- No technical knowledge needed

**For Long-term:** PHP Backend (Option 2)
- Once you have hosting setup
- No ongoing costs
- Full control

---

## Current Form Configuration

The contact form in `contact.html` is currently configured for Formspree. 

### Form Fields:
- Parent/Guardian Name (required)
- Email Address (required)
- Phone Number (required)
- Child's Name (optional)
- Child's Age (optional)
- Program of Interest (dropdown)
- Inquiry Type (dropdown)
- Message (required)
- Consent checkbox (required)

### Email Template:
Submissions will include all field data in a formatted email.

---

## Need Help?

If you need assistance with email integration:
1. Contact your web hosting provider
2. Refer to service documentation (Formspree, EmailJS)
3. Or hire a developer for custom integration

---

**Recommended Action:**
Start with Formspree for immediate functionality, then migrate to PHP backend once comfortable with hosting setup.
