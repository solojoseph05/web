/**
 * Dynamic Restaurant Placeholder Image Generator
 * Generates custom SVG data URIs styled for Pollo Feliz menu items
 * when no photo URL is available in the data or if loading fails.
 */

export const generateFoodPlaceholder = (item, width = 600, height = 400) => {
  const name = item?.name || 'Especialidad de la Casa'
  const category = item?.category || 'Pollo Feliz'

  const nameLower = name.toLowerCase()
  const catLower = category.toLowerCase()

  // Theme palettes and icons based on Mexican food categories
  let icon = '🍗'
  let bgStart = '#991b1b' // deep crimson red
  let bgEnd = '#450a0a'   // charcoal ember
  let accentColor = '#fca5a5'

  if (
    catLower.includes('bebida') ||
    nameLower.includes('refresco') ||
    nameLower.includes('agua') ||
    nameLower.includes('horchata') ||
    nameLower.includes('jamaica') ||
    nameLower.includes('té')
  ) {
    icon = '🥤'
    bgStart = '#b91c1c'
    bgEnd = '#7f1d1d'
    accentColor = '#fecaca'
  } else if (
    catLower.includes('postre') ||
    nameLower.includes('flan') ||
    nameLower.includes('arroz') ||
    nameLower.includes('pay') ||
    nameLower.includes('jericalla')
  ) {
    icon = '🍮'
    bgStart = '#92400e' // caramel amber
    bgEnd = '#451a03'
    accentColor = '#fde68a'
  } else if (nameLower.includes('hamburguesa')) {
    icon = '🍔'
    bgStart = '#854d0e'
    bgEnd = '#422006'
    accentColor = '#fed7aa'
  } else if (catLower.includes('taco') || nameLower.includes('quesadilla')) {
    icon = '🌮'
    bgStart = '#9a3412'
    bgEnd = '#431407'
    accentColor = '#fed7aa'
  } else if (nameLower.includes('ensalada') || catLower.includes('ensalada')) {
    icon = '🥗'
    bgStart = '#166534' // fresh herbs green
    bgEnd = '#052e16'
    accentColor = '#bbf7d0'
  } else if (nameLower.includes('papa') || nameLower.includes('nugget') || catLower.includes('complemento')) {
    icon = '🍟'
    bgStart = '#854d0e'
    bgEnd = '#422006'
    accentColor = '#fef08a'
  } else if (catLower.includes('paquete')) {
    icon = '🍱'
    bgStart = '#991b1b'
    bgEnd = '#3f0c0c'
    accentColor = '#fca5a5'
  }

  // XML sanitation
  const cleanName = String(name)
    .replace(/&/g, '&amp;')
    .replace(/</g, '&lt;')
    .replace(/>/g, '&gt;')
    .replace(/"/g, '&quot;')

  const cleanCategory = String(category)
    .replace(/&/g, '&amp;')
    .replace(/</g, '&lt;')
    .replace(/>/g, '&gt;')
    .replace(/"/g, '&quot;')

  const svg = `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 ${width} ${height}" width="${width}" height="${height}">
    <defs>
      <linearGradient id="foodGrad" x1="0%" y1="0%" x2="100%" y2="100%">
        <stop offset="0%" stop-color="${bgStart}"/>
        <stop offset="100%" stop-color="${bgEnd}"/>
      </linearGradient>
      <pattern id="dotPattern" width="36" height="36" patternUnits="userSpaceOnUse">
        <circle cx="18" cy="18" r="1.5" fill="rgba(255,255,255,0.07)"/>
      </pattern>
    </defs>
    
    <!-- Gradient & subtle texture -->
    <rect width="${width}" height="${height}" fill="url(#foodGrad)"/>
    <rect width="${width}" height="${height}" fill="url(#dotPattern)"/>
    
    <!-- Concentric glow circles -->
    <circle cx="${width / 2}" cy="${height / 2 - 25}" r="75" fill="rgba(255,255,255,0.06)"/>
    <circle cx="${width / 2}" cy="${height / 2 - 25}" r="55" fill="rgba(255,255,255,0.08)"/>
    
    <!-- Dish Emoji / Icon -->
    <text x="${width / 2}" y="${height / 2 - 10}" font-size="56" text-anchor="middle" dominant-baseline="middle">${icon}</text>
    
    <!-- Category Header -->
    <text x="${width / 2}" y="${height / 2 + 56}" font-family="system-ui, -apple-system, sans-serif" font-size="12" font-weight="700" fill="${accentColor}" text-anchor="middle" letter-spacing="2" text-transform="uppercase">${cleanCategory}</text>
    
    <!-- Dish Name -->
    <text x="${width / 2}" y="${height / 2 + 88}" font-family="system-ui, -apple-system, sans-serif" font-size="20" font-weight="800" fill="#ffffff" text-anchor="middle">${cleanName}</text>
    
    <!-- Brand badge on bottom -->
    <rect x="${width / 2 - 50}" y="${height - 36}" width="100" height="22" rx="11" fill="rgba(0,0,0,0.35)"/>
    <text x="${width / 2}" y="${height - 21}" font-family="system-ui, -apple-system, sans-serif" font-size="10" font-weight="700" fill="rgba(255,255,255,0.85)" text-anchor="middle" letter-spacing="1">POLLO FELIZ</text>
  </svg>`

  return `data:image/svg+xml;charset=utf-8,${encodeURIComponent(svg)}`
}
