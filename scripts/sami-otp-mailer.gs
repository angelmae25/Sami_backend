function authorizeMailer() {
  MailApp.getRemainingDailyQuota();
}

const TOKEN_PROPERTY = 'SAMI_OTP_TOKEN';
const OTP_COOLDOWN_SECONDS = 60;

function doPost(e) {
  try {
    const request = JSON.parse((e && e.postData && e.postData.contents) || '{}');
    const expectedToken = PropertiesService.getScriptProperties().getProperty(TOKEN_PROPERTY) || '';
    const suppliedToken = String(request.token || '');

    if (!expectedToken || !constantTimeEquals(expectedToken, suppliedToken)) {
      return json({ ok: false, error: 'unauthorized' });
    }

    if (request.action === 'health') {
      return json({ ok: true, service: 'SAMI OTP mailer' });
    }

    const recipient = String(request.to || '').trim();
    const code = String(request.code || '').trim();
    if (!/^[^\s@]+@tup\.edu\.ph$/i.test(recipient) || !/^\d{6}$/.test(code)) {
      return json({ ok: false, error: 'invalid_request' });
    }

    const cacheKey = 'otp:' + Utilities.base64EncodeWebSafe(
      Utilities.computeDigest(Utilities.DigestAlgorithm.SHA_256, recipient.toLowerCase())
    );
    const cache = CacheService.getScriptCache();
    if (cache.get(cacheKey)) {
      return json({ ok: false, error: 'cooldown' });
    }

    MailApp.sendEmail({
      to: recipient,
      subject: 'Your SAMI verification code',
      body: 'Your SAMI verification code is ' + code + '. It expires in five minutes. If you did not request this code, you can ignore this email.',
      name: 'SAMI'
    });
    cache.put(cacheKey, 'sent', OTP_COOLDOWN_SECONDS);

    return json({ ok: true });
  } catch (error) {
    return json({ ok: false, error: 'send_failed' });
  }
}

function constantTimeEquals(left, right) {
  if (left.length !== right.length) return false;
  let difference = 0;
  for (let i = 0; i < left.length; i++) {
    difference |= left.charCodeAt(i) ^ right.charCodeAt(i);
  }
  return difference === 0;
}

function json(value) {
  return ContentService
    .createTextOutput(JSON.stringify(value))
    .setMimeType(ContentService.MimeType.JSON);
}

