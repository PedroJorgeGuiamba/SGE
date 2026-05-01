#!/usr/bin/env python3
import sys, json, io
from datetime import datetime

import matplotlib
matplotlib.use('Agg')
import matplotlib.pyplot as plt
import numpy as np

from reportlab.lib.pagesizes import A4, landscape
from reportlab.lib import colors
from reportlab.lib.units import cm
from reportlab.lib.styles import ParagraphStyle
from reportlab.lib.enums import TA_CENTER, TA_JUSTIFY
from reportlab.platypus import (
    SimpleDocTemplate, Paragraph, Spacer, Table, TableStyle,
    Image, HRFlowable, PageBreak
)
from reportlab.pdfgen import canvas as pdfcanvas
import urllib.request
import requests

PRETO      = colors.black
CINZA_LINHA = colors.HexColor('#AAAAAA')
CINZA_LT   = colors.HexColor('#F0F0F0')
AZUL_ITC   = colors.HexColor('#003366')
BRANCO     = colors.white

def s(name, **kw):
    return ParagraphStyle(name, **kw)

ST = {
    'inst':    s('inst',  fontName='Times-Bold',   fontSize=14, textColor=AZUL_ITC,  leading=18, spaceAfter=2),
    'sub':     s('sub',   fontName='Times-Bold',   fontSize=11, textColor=AZUL_ITC,  leading=14, spaceAfter=2),
    'meta':    s('meta',  fontName='Times-Roman',  fontSize=10, textColor=PRETO,     leading=13, spaceAfter=0),
    'secao':   s('secao', fontName='Times-Bold',   fontSize=11, textColor=PRETO,     spaceBefore=10, spaceAfter=4, leading=15),
    'resumo':  s('res',   fontName='Times-Roman',  fontSize=10, textColor=PRETO,     spaceAfter=6, leading=14, alignment=TA_JUSTIFY),
    'th':      s('th',    fontName='Times-Bold',   fontSize=9,  textColor=BRANCO,    alignment=TA_CENTER),
    'td':      s('td',    fontName='Times-Roman',  fontSize=9,  textColor=PRETO,     alignment=TA_CENTER),
    'td_tot':  s('tdt',   fontName='Times-Bold',   fontSize=9,  textColor=BRANCO,    alignment=TA_CENTER),
    'rodape':  s('rod',   fontName='Times-Roman',  fontSize=8,  textColor=colors.HexColor('#666666'), alignment=TA_CENTER),
}

def grafico_barras(labels, data, cor, w_cm, h_cm):
    fig, ax = plt.subplots(figsize=(w_cm/2.54, h_cm/2.54), dpi=130)
    fig.patch.set_facecolor('white')
    ax.set_facecolor('#F8F8F8')
    x = np.arange(len(labels))
    bars = ax.bar(x, data, color=cor, edgecolor='white', width=0.6, zorder=3)
    for bar, v in zip(bars, data):
        if v > 0:
            ax.text(bar.get_x()+bar.get_width()/2, bar.get_height()+0.05,
                    str(int(v)), ha='center', va='bottom', fontsize=8, color='#222222')
    ax.set_xticks(x)
    ax.set_xticklabels([str(l) for l in labels], fontsize=8, color='#333333')
    ax.yaxis.grid(True, linestyle='--', alpha=0.4, color='#CCCCCC', zorder=0)
    ax.set_axisbelow(True)
    for sp in ['top','right']: ax.spines[sp].set_visible(False)
    for sp in ['left','bottom']: ax.spines[sp].set_color('#CCCCCC')
    ax.tick_params(colors='#555555', labelsize=8)
    ax.set_ylabel('Quantidade', fontsize=9, color='#444444')
    max_v = max(data) if data and max(data) > 0 else 1
    ax.set_ylim(0, max_v*1.3)
    plt.tight_layout(pad=0.6)
    buf = io.BytesIO()
    plt.savefig(buf, format='png', bbox_inches='tight', facecolor='white')
    plt.close(fig)
    buf.seek(0)
    return buf

class RelCanvas(pdfcanvas.Canvas):
    def __init__(self, filename, periodo_str, **kw):
        super().__init__(filename, **kw)
        self._periodo = periodo_str
        self._n = 0
    def showPage(self):
        self._n += 1; self._rodape(); super().showPage()
    def save(self):
        self._n += 1; self._rodape(); super().save()
    def _rodape(self):
        w, _ = self._pagesize
        self.saveState()
        self.setStrokeColor(CINZA_LINHA)
        self.line(1.5*cm, 1.2*cm, w-1.5*cm, 1.2*cm)
        self.setFont('Times-Roman', 8)
        self.setFillColor(colors.HexColor('#666666'))
        agora = datetime.now().strftime('%d/%m/%Y às %H:%M')
        self.drawCentredString(w/2, 0.7*cm,
            f"Relatório gerado em {agora}  |  Instituto de Transportes e Comunicações  |  Pág. {self._n}")
        self.restoreState()

def gerar_pdf(dados, output_path):
    periodo   = dados.get('periodo','anual')
    titulo    = dados.get('titulo_periodo','')
    labels    = dados.get('labels',[])
    c_data    = dados.get('cartas_data',[])
    cr_data   = dados.get('cred_data',[])
    total_c   = dados.get('total_cartas',0)
    total_cr  = dados.get('total_credenciais',0)

    page_sz = landscape(A4)
    pw, ph  = page_sz
    mg      = 1.8*cm
    cw      = pw - 2*mg

    doc = SimpleDocTemplate(output_path, pagesize=page_sz,
        leftMargin=mg, rightMargin=mg, topMargin=mg, bottomMargin=2.2*cm,
        title=f"Relatório – {titulo}", author="ITC")

    story = []

    # Cabeçalho
    logo_img = None
    try:
        url = "https://www.itc.ac.mz/wp-content/uploads/2020/07/cropped-LOGO_ITC-09.png"
        req = urllib.request.Request(url, headers={'User-Agent':'Mozilla/5.0'})
        # raw = urllib.request.urlopen(req, timeout=5).read()
        response = requests.get(url, timeout=5, verify=True)
        response.raise_for_status()
        raw = response.content
        logo_img = Image(io.BytesIO(raw), width=2.0*cm, height=1.8*cm)
    except Exception:
        pass

    txt_col = [
        Paragraph("INSTITUTO DE TRANSPORTES E COMUNICAÇÕES", ST['inst']),
        Paragraph("Relatório de Pedidos de Cartas de Estágio e Credenciais", ST['sub']),
        Paragraph(f"<b>Período:</b> {titulo}", ST['meta']),
    ]
    if logo_img:
        hdr = Table([[logo_img, txt_col]], colWidths=[2.5*cm, cw-2.5*cm])
        hdr.setStyle(TableStyle([('VALIGN',(0,0),(-1,-1),'MIDDLE'),
                                 ('LEFTPADDING',(0,0),(-1,-1),0),
                                 ('RIGHTPADDING',(0,0),(-1,-1),0),
                                 ('BOTTOMPADDING',(0,0),(-1,-1),4)]))
    else:
        hdr = Table([[txt_col]], colWidths=[cw])
        hdr.setStyle(TableStyle([('LEFTPADDING',(0,0),(-1,-1),0)]))
    story.append(hdr)
    story.append(HRFlowable(width="100%", thickness=1.5, color=AZUL_ITC, spaceAfter=10))

    # Caixas de totais
    taxa = round(total_cr/total_c*100,1) if total_c > 0 else 0.0

    def caixa(lbl, val):
        t = Table([
            [Paragraph(f'<b>{lbl}</b>', ParagraphStyle('cl', fontName='Times-Bold', fontSize=9, textColor=PRETO, alignment=TA_CENTER))],
            [Paragraph(f'<b>{val}</b>', ParagraphStyle('cv', fontName='Times-Bold', fontSize=22, textColor=PRETO, alignment=TA_CENTER, leading=26))],
        ], colWidths=[cw/3 - 0.5*cm])
        t.setStyle(TableStyle([
            ('BOX',         (0,0),(-1,-1),0.8,PRETO),
            ('LINEBELOW',   (0,0),(-1,0), 0.8,PRETO),
            ('TOPPADDING',  (0,0),(-1,-1),8),
            ('BOTTOMPADDING',(0,0),(-1,-1),8),
        ]))
        return t

    caixas = Table([[caixa("Total de Cartas Solicitadas",str(total_c)),
                     caixa("Total de Credenciais Emitidas",str(total_cr)),
                     caixa("Taxa de Credencialização",f"{taxa}%")]],
                   colWidths=[cw/3]*3)
    caixas.setStyle(TableStyle([('LEFTPADDING',(0,0),(-1,-1),4),('RIGHTPADDING',(0,0),(-1,-1),4),
                                ('TOPPADDING',(0,0),(-1,-1),0),('BOTTOMPADDING',(0,0),(-1,-1),0)]))
    story.append(caixas)
    story.append(Spacer(1,12))

    unid = 'dia' if periodo=='mensal' else 'mês'

    # Secção 1
    story.append(HRFlowable(width="100%", thickness=0.5, color=CINZA_LINHA, spaceAfter=5))
    story.append(Paragraph("1. Evolução de Pedidos de Cartas de Estágio", ST['secao']))
    max_c = max(c_data) if c_data else 0
    pico_c = labels[c_data.index(max_c)] if max_c>0 and labels else '—'
    atv_c  = [v for v in c_data if v>0]
    media_c = round(sum(atv_c)/len(atv_c),1) if atv_c else 0
    txt_c = f"No período de <b>{titulo}</b> foram registados <b>{total_c} pedidos de cartas de estágio</b>. "
    if max_c > 0:
        txt_c += f"O maior volume ocorreu no {unid} <b>{pico_c}</b> com <b>{max_c} pedido(s)</b>, sendo a média por {unid} activo de <b>{media_c}</b>. "
    txt_c += ("Este indicador reflecte a procura de estágio por parte dos alunos no período em análise."
              if total_c>0 else "Não foram registados pedidos neste período.")
    story.append(Paragraph(txt_c, ST['resumo']))
    story.append(Image(grafico_barras(labels, c_data, '#336699', cw/cm, 8), width=cw, height=8*cm))
    story.append(Spacer(1,10))

    # Secção 2
    story.append(HRFlowable(width="100%", thickness=0.5, color=CINZA_LINHA, spaceAfter=5))
    story.append(Paragraph("2. Evolução de Credenciais Emitidas", ST['secao']))
    max_cr = max(cr_data) if cr_data else 0
    pico_cr = labels[cr_data.index(max_cr)] if max_cr>0 and labels else '—'
    atv_cr  = [v for v in cr_data if v>0]
    media_cr = round(sum(atv_cr)/len(atv_cr),1) if atv_cr else 0
    pendentes = total_c - total_cr
    txt_cr = f"No mesmo período foram emitidas <b>{total_cr} credenciais de estágio</b>, correspondendo a uma taxa de credencialização de <b>{taxa}%</b> em relação às cartas solicitadas. "
    if max_cr > 0:
        txt_cr += f"O pico de emissão verificou-se no {unid} <b>{pico_cr}</b> com <b>{max_cr} credencial(is)</b>, sendo a média por {unid} activo de <b>{media_cr}</b>. "
    if total_cr == 0:
        txt_cr = f"Não foram emitidas credenciais neste período. Encontram-se <b>{pendentes} processo(s)</b> a aguardar credencialização."
    elif pendentes > 0:
        txt_cr += f"Permanecem pendentes de credencial <b>{pendentes} processo(s)</b>."
    story.append(Paragraph(txt_cr, ST['resumo']))
    story.append(Image(grafico_barras(labels, cr_data, '#2E8B57', cw/cm, 8), width=cw, height=8*cm))

    # Secção 3 – tabela
    story.append(PageBreak())
    story.append(HRFlowable(width="100%", thickness=0.5, color=CINZA_LINHA, spaceAfter=5))
    story.append(Paragraph("3. Dados Detalhados do Período", ST['secao']))
    story.append(Paragraph(
        f"A tabela seguinte apresenta o detalhe numérico de pedidos e credenciais "
        f"para cada {'dia' if periodo=='mensal' else 'mês'} de <b>{titulo}</b>, "
        f"incluindo o saldo de processos pendentes de credencialização.",
        ST['resumo']
    ))

    uc = 'Dia' if periodo=='mensal' else 'Mês'
    tbl = [[Paragraph(f'<b>{uc}</b>',ST['th']),
            Paragraph('<b>Cartas de Estágio</b>',ST['th']),
            Paragraph('<b>Credenciais Emitidas</b>',ST['th']),
            Paragraph('<b>Pendentes</b>',ST['th'])]]

    for i,lbl in enumerate(labels):
        cv  = c_data[i]  if i<len(c_data)  else 0
        crv = cr_data[i] if i<len(cr_data) else 0
        tbl.append([Paragraph(str(lbl),ST['td']),
                    Paragraph(str(cv),ST['td']),
                    Paragraph(str(crv),ST['td']),
                    Paragraph(str(cv-crv),ST['td'])])

    n = len(tbl)
    tbl.append([Paragraph('<b>TOTAL</b>',ST['td_tot']),
                Paragraph(f'<b>{total_c}</b>',ST['td_tot']),
                Paragraph(f'<b>{total_cr}</b>',ST['td_tot']),
                Paragraph(f'<b>{total_c-total_cr}</b>',ST['td_tot'])])

    dt = Table(tbl, colWidths=[cw/4]*4, repeatRows=1)
    dt.setStyle(TableStyle([
        ('BACKGROUND',    (0,0), (-1,0),  AZUL_ITC),
        ('BACKGROUND',    (0,n), (-1,n),  AZUL_ITC),
        ('ROWBACKGROUNDS',(0,1), (-1,n-1),[CINZA_LT,BRANCO]),
        ('GRID',          (0,0), (-1,-1), 0.4, CINZA_LINHA),
        ('VALIGN',        (0,0), (-1,-1), 'MIDDLE'),
        ('TOPPADDING',    (0,0), (-1,-1), 5),
        ('BOTTOMPADDING', (0,0), (-1,-1), 5),
        ('LINEABOVE',     (0,n), (-1,n),  1.0, PRETO),
    ]))
    story.append(dt)

    def make_canvas(filename, **kwargs):
        return RelCanvas(filename, titulo, pagesize=page_sz)

    doc.build(story, canvasmaker=make_canvas)
    print(f"PDF gerado: {output_path}")

if __name__ == '__main__':
    if len(sys.argv) >= 3:
        dados  = json.loads(sys.argv[1]); output = sys.argv[2]
    else:
        raw = json.loads(sys.stdin.read()); dados = raw['dados']; output = raw['output']
    gerar_pdf(dados, output)