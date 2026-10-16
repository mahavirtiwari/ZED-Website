<p>A query was sent with the Contact Us form on the ZED website.</p>
<table cellpadding="4">
    <tr><th align="left">Name</th><td>{{ $contact->contact_name }}</td></tr>
    <tr><th align="left">E-mail</th><td>{{ $contact->contact_email }}</td></tr>
    <tr><th align="left">Phone</th><td>{{ $contact->contact_phone ?: '—' }}</td></tr>
    <tr><th align="left">Enterprise</th><td>{{ $contact->enterprise_name ?: '—' }}</td></tr>
    <tr><th align="left">Category</th><td>{{ $contact->query_category }}</td></tr>
</table>
<p><strong>Message</strong><br>{!! nl2br(e($contact->contact_query)) !!}</p>
<p>It is also listed in the CMS under Contact queries.</p>
