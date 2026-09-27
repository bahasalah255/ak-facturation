

import { useMemo, useState } from 'react'
import './App.css'

const initialInvoice = {
	companyName: 'AK RAPID TRANS SARL', companySubtitle: 'TRANSPORT DE MARCHANDISES',
	companyAddress: 'AV HASSAN II RES NORA IMM D1 APPT 4 RDC MARTIL', companyDetails: 'RC: 38841  |  Patente: 51806955  |  N.I.F: 68777041  |  ICE: 003826561000075  |  CNSS: 6486368',
	invoiceNumber: '1/2026', invoiceDate: '2026-03-31', client: 'SUPER CERAME', clientIce: '00000505000042', taxRate: 10,
	lines: [{ bl: '20 681', date: '2026-03-31', designation: 'TETOUAN - CASA', truck: '16069/A/75', amount: 3520 }],
}

const formatMoney = (value) => new Intl.NumberFormat('fr-FR', { minimumFractionDigits: 2, maximumFractionDigits: 2 }).format(value)
const formatDate = (value) => value ? new Intl.DateTimeFormat('fr-FR').format(new Date(`${value}T00:00:00`)) : '--/--/----'

function App() {
	const [invoice, setInvoice] = useState(initialInvoice)
	const subtotal = useMemo(() => invoice.lines.reduce((sum, line) => sum + (Number(line.amount) || 0), 0), [invoice.lines])
	const tax = subtotal * (Number(invoice.taxRate) || 0) / 100
	const total = subtotal + tax
	const updateInvoice = (field, value) => setInvoice((current) => ({ ...current, [field]: value }))
	const updateLine = (index, field, value) => setInvoice((current) => ({ ...current, lines: current.lines.map((line, lineIndex) => lineIndex === index ? { ...line, [field]: value } : line) }))
	const addLine = () => setInvoice((current) => ({ ...current, lines: [...current.lines, { bl: '', date: current.invoiceDate, designation: '', truck: '', amount: 0 }] }))
	const removeLine = (index) => setInvoice((current) => ({ ...current, lines: current.lines.filter((_, lineIndex) => lineIndex !== index) }))

	return (
		<main className="app-shell">
			<header className="topbar"><div className="brand-mark">AK</div><div className="brand-copy"><strong>AK Facturation</strong><span>Transport de marchandises</span></div><div className="topbar-actions"><span className="status-dot" /> Brouillon <button className="button button-dark" onClick={() => window.print()}>Imprimer</button></div></header>
			<section className="workspace-heading"><div><p className="eyebrow">Nouvelle facture</p><h1>Préparer une facture</h1><p className="heading-note">Saisissez les informations ci-dessous. Le document se met à jour instantanément.</p></div><button className="button button-quiet" onClick={() => setInvoice(initialInvoice)}>Réinitialiser</button></section>
			<div className="workspace-grid">
				<section className="editor-panel">
					<div className="panel-section"><div className="section-heading"><span className="section-number">01</span><div><h2>Informations générales</h2><p>Identité et référence de la facture</p></div></div><div className="field-grid two-columns"><label>Numéro de facture<input value={invoice.invoiceNumber} onChange={(event) => updateInvoice('invoiceNumber', event.target.value)} /></label><label>Date<input type="date" value={invoice.invoiceDate} onChange={(event) => updateInvoice('invoiceDate', event.target.value)} /></label><label className="wide-field">Client<input value={invoice.client} onChange={(event) => updateInvoice('client', event.target.value)} /></label><label className="wide-field">ICE client<input value={invoice.clientIce} onChange={(event) => updateInvoice('clientIce', event.target.value)} /></label></div></div>
					<div className="panel-section"><div className="section-heading"><span className="section-number">02</span><div><h2>Prestations</h2><p>Ajoutez chaque bon de livraison transporté</p></div></div><div className="line-editor">{invoice.lines.map((line, index) => <div className="line-row" key={`${index}-${line.bl}`}><div className="line-row-head"><span>Ligne {String(index + 1).padStart(2, '0')}</span>{invoice.lines.length > 1 && <button className="remove-button" onClick={() => removeLine(index)}>Supprimer</button>}</div><div className="field-grid line-fields"><label>BL n°<input value={line.bl} onChange={(event) => updateLine(index, 'bl', event.target.value)} /></label><label>Date BL<input type="date" value={line.date} onChange={(event) => updateLine(index, 'date', event.target.value)} /></label><label className="wide-field">Désignation<input value={line.designation} onChange={(event) => updateLine(index, 'designation', event.target.value)} /></label><label>Camion n°<input value={line.truck} onChange={(event) => updateLine(index, 'truck', event.target.value)} /></label><label>Montant HT<div className="input-with-suffix"><input type="number" min="0" step="0.01" value={line.amount} onChange={(event) => updateLine(index, 'amount', event.target.value)} /><span>MAD</span></div></label></div></div>)}</div><button className="add-line" onClick={addLine}><span>+</span> Ajouter une prestation</button></div>
					<div className="panel-section panel-section-last"><div className="section-heading"><span className="section-number">03</span><div><h2>Fiscalité</h2><p>Configurez le taux de TVA applicable</p></div></div><label className="short-field">Taux de TVA<div className="input-with-suffix"><input type="number" min="0" value={invoice.taxRate} onChange={(event) => updateInvoice('taxRate', event.target.value)} /><span>%</span></div></label></div>
				</section>
				<section className="preview-column"><div className="preview-label"><span>Aperçu du document</span><span>Format A4</span></div><article className="invoice-paper"><div className="invoice-head"><div><h2>{invoice.companyName}</h2><p>{invoice.companySubtitle}</p></div><div className="logo-placeholder">AK <small>RAPID TRANS</small></div></div><div className="invoice-meta"><span>Date: <strong>{formatDate(invoice.invoiceDate)}</strong></span><span>Facture N°: <strong>{invoice.invoiceNumber || '—'}</strong></span></div><div className="client-block"><p>Client: <strong>{invoice.client || '—'}</strong></p><p>ICE N°: <strong>{invoice.clientIce || '—'}</strong></p></div><table><thead><tr><th>BL N°</th><th>DATE BL</th><th>DÉSIGNATION</th><th>CAMION N°</th><th className="amount-cell">TOTAL H.T</th></tr></thead><tbody>{invoice.lines.map((line, index) => <tr key={index}><td>{line.bl || '—'}</td><td>{formatDate(line.date)}</td><td>{line.designation || '—'}</td><td>{line.truck || '—'}</td><td className="amount-cell">{formatMoney(Number(line.amount) || 0)}</td></tr>)}</tbody></table><div className="totals-area"><div className="amount-words">arrêter la présente Facture à la somme de:<strong>{formatMoney(total)} dirhams</strong></div><div className="totals"><div><span>HT</span><strong>{formatMoney(subtotal)}</strong></div><div><span>TVA {invoice.taxRate}%</span><strong>{formatMoney(tax)}</strong></div><div className="grand-total"><span>TTC</span><strong>{formatMoney(total)}</strong></div></div></div><div className="signature">signé:</div><footer><p>{invoice.companyAddress}</p><p>{invoice.companyDetails}</p></footer></article></section>
			</div>
		</main>
	)
}

export default App
