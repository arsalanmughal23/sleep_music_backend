import smtplib
from email.mime.text import MIMEText
from email.mime.multipart import MIMEMultipart
from email.mime.application import MIMEApplication
import json

# Email configuration
smtp_server = 'smtp.gmail.com'
smtp_port = 587  # Use the appropriate SMTP port
smtp_username = 'devops@tekrevol.com'
smtp_password = 'owhzbposugsylscu'
sender_email = 'devops@tekrevol.com'
recipient_emails = ['adil.khursheed@tekrevol.com', 'faiz@tekrevol.com', 'arsalan.mughal@tekrevol.com']  # Add more recipient emails as needed

# Create the email message
message = MIMEMultipart()
message['From'] = sender_email
message['To'] = ', '.join(recipient_emails)  # Join recipient emails as a comma-separated string

# Check if the Quality Gate passed or failed
with open('result.json', 'r') as json_file:
    data = json.load(json_file)

quality_gate_status = data.get('projectStatus', {}).get('status', '')

if quality_gate_status == 'ERROR':
    message['Subject'] = 'Pipeline Failure Sleep-Meditation'
    body = 'The pipeline has failed Sleep-Meditation. Quality gate not passed.\n\n'
else:
    message['Subject'] = 'Pipeline Failure Sleep-Meditation'
    body = 'The pipeline has failed Sleep-Meditation. Quality gate not passed.\n\n'

message.attach(MIMEText(body, 'plain'))

# Include the table in the email body
issues = data.get('issues', [])

if issues:
    table = (
        "<table border='1' style='border-collapse:collapse;'>"
        "<tr><th>Key</th><th>Rule</th><th>Severity</th><th>Component</th><th>Message</th></tr>"
    )

    for issue in issues:
        key = issue['key']
        rule = issue['rule']
        severity = issue['severity']
        component = issue['component']
        message_text = issue['message']

        table += (
            f"<tr><td>{key}</td><td>{rule}</td><td>{severity}</td><td>{component}</td><td>{message_text}</td></tr>"
        )

    table += "</table>"

    # Create a new MIMEText object to attach the HTML content
    table_content = MIMEText(table, 'html')
    message.attach(table_content)

# Attach the report file
with open('result.json', 'rb') as attachment:
    part = MIMEApplication(attachment.read())
    part.add_header('Content-Disposition', f'attachment; filename="result.json"')
    message.attach(part)

# Connect to the SMTP server and send the email
with smtplib.SMTP(smtp_server, smtp_port) as server:
    server.starttls()
    server.login(smtp_username, smtp_password)
    server.sendmail(sender_email, recipient_emails, message.as_string())

print("Email sent.")
