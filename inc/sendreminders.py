#!/usr/bin/python

import re
import MySQLdb
import smtplib

from datetime import datetime, date, timedelta
from email.mime.multipart import MIMEMultipart
from email.mime.text import MIMEText

def main():
    con = MySQLdb.connect('localhost', 'flb', 'WelcomeFlbSinger', 'flb', unix_socket='/opt/lampp/var/mysql/mysql.sock')

    datetimeStr = datetime.now().strftime('%Y-%m-%d %H:%M:00')
    query = """
SELECT u.email, r.title, sm.local_time, sr.description
FROM   users u, recipes r, scheduled_makes sm, scheduled_reminders sr
WHERE  sr.server_time = '%s'
AND    sr.scheduled_make_id = sm.scheduled_make_id
AND    sm.user_id = u.user_id
AND    sm.recipe_id = r.recipe_id
""" % datetimeStr

    with con:
        cur = con.cursor(MySQLdb.cursors.DictCursor)
        print "Looking for reminders as of %s." % datetimeStr
        cur.execute(query)

        rows = cur.fetchall()

        for row in rows:
            sendmail(row["email"], row["title"], row["local_time"], row["description"])


def sendmail(email, recipeTitle, makeTime, reminderText):
    print "Sending email to %s about %s at %s (local time) for %s" % (email, recipeTitle, makeTime, reminderText)
    reminderTextFormatted = exclaim(reminderText)
    makeTimeDesc = getFuzzyDayName(makeTime.date()) + " " + getFuzzyTimeOfDay(makeTime.hour)

    text = """
Hey, Fig Leaf Betty here reminding you that it's time to do, you know, something related to the %s you're making %s. %s
""" % (recipeTitle, makeTimeDesc, reminderTextFormatted)
    html = """
<html>
  <head></head>
  <body>
    <p>Hey, Fig Leaf Betty here reminding you that it's time to do, you know, something related to the %s you're making %s.<br>
 
       <div style="
    max-width: 700px;
    border-style: dashed;
    border-color: gray;
    border-width: 2px;
    background-color: lightgreen;
    margin: 20px;
    padding: 10px;
">%s</div>

       Keep it real,<br>
       Betty
    </p>
  </body>
""" % (recipeTitle, makeTimeDesc, reminderTextFormatted)

    part1 = MIMEText(text, 'plain')
    part2 = MIMEText(html, 'html')

    fromaddr = 'Fig Leaf Betty\'s Recipe Reminders <reminders@figleafbetty.com>'
    toaddrs  = email

    msg = MIMEMultipart('alternative')
    msg['Subject'] = reminderTextFormatted
    msg['From'] = fromaddr
    msg['To'] = toaddrs

    msg.attach(part1)
    msg.attach(part2)

    username = 'figleafbetty@gmail.com'
    password = 'maclovesbetty'

    server = smtplib.SMTP('smtp.gmail.com:587')
    server.starttls()
    server.login(username, password)
    server.sendmail(fromaddr, toaddrs, msg.as_string())
    server.quit()


def exclaim(text):
    return re.sub('(\\.|!)$', '', text) + "!"


def getFuzzyTimeOfDay(hour):
    if (hour < 12): return 'morning'
    if (hour < 17): return 'afternoon'

    return 'evening'


def getFuzzyDayName(d):
    today = date.today()

    if (d == today): return 'this'
    if (d - today == timedelta(days=1)): return 'tomorrow'

    return "on %s" % d.strftime("%A")


if __name__ == '__main__':
    main()

