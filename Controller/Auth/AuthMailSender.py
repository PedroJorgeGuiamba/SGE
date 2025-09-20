import smtplib
import sys
from email.mime.text import MIMEText
from email.mime.multipart import MIMEMultipart
import os

def enviar_email(email_destino, otp):
    corpo_email = f"O seu código é {otp}"

    msg = MIMEMultipart()
    msg['From'] = "devzitc@gmail.com"
    msg['To'] = email_destino
    msg['Subject'] = "Código de Verificação"

    msg.attach(MIMEText(corpo_email, 'plain'))

    try:
        servidor = smtplib.SMTP("smtp.gmail.com", 587)
        servidor.starttls()
        servidor.login(msg['From'], os.getenv('EMAIL_PASS'))
        servidor.sendmail(msg['From'], msg['To'], msg.as_string())
        servidor.quit()
        print("Email enviado com sucesso!")
    except Exception as e:
        print(f"Erro ao enviar email: {e}")
        sys.exit(1)

if __name__ == "__main__":
    if len(sys.argv) != 3:
        print("Uso: python AuthMailSender.py <email> <otp>")
        sys.exit(1)
    
    email_destino = sys.argv[1]
    otp = sys.argv[2]
    enviar_email(email_destino, otp)