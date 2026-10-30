// src/utils/menuItemDetails.js
// Proporciona ingredientes detallados e información de alérgenos para el menú de Pollo Feliz.

export function getItemIngredients(item) {
  if (!item) return []
  if (Array.isArray(item.ingredients) && item.ingredients.length > 0) {
    return item.ingredients
  }

  const name = (item.name || '').toLowerCase()
  const cat = (item.category || '').toLowerCase()

  // Pollo Entero o Medio Pollo
  if (name.includes('pollo entero') || name.includes('medio pollo') || cat.includes('pollo') || name.includes('brasa')) {
    return [
      'Pollo fresco seleccionado de primera calidad',
      'Marinado artesanal con jugo de cítricos (naranja agria, limón) y especias naturales',
      'Ajo asado, orégano silvestre, comino y paprika española',
      'Sal de grano de Colima',
      'Asado lentamente al calor de carbón de mezquite 100% natural',
      'Guarnición: Tortillas de maíz nixtamalizado hechas en comal',
      'Guarnición: Cebollitas cambray caramelizadas al carbón y chiles toreados',
      'Guarnición: Salsa verde de tomatillo milpero y salsa roja tatemada casera',
    ]
  }

  // Paquetes o Combos
  if (cat.includes('paquete') || cat.includes('combo') || name.includes('paquete') || name.includes('familiar')) {
    return [
      'Pollo entero marinado con la receta tradicional secreta',
      'Asado al carbón de mezquite',
      'Complemento 1: Papas a la francesa o papas gajo sazonadas',
      'Complemento 2: Ensalada fresca de col cremosa o coditos con jamón',
      'Tortillas calientes recién salidas del comal',
      'Refresco de 2 litros bien helado',
      'Surtido de salsas de la casa y limones frescos',
    ]
  }

  // Papas
  if (name.includes('papa') || name.includes('francesa') || name.includes('gajo')) {
    return [
      'Papas seleccionadas corte rústico o bastón',
      'Aceite vegetal 100% puro libre de grasas trans',
      'Sal de mar refinada',
      'Páprika ahumada, ajo en polvo y pimienta negra molida',
      'Acompañamiento: Salsa catsup y mayonesa con chipotle',
    ]
  }

  // Tacos & Quesadillas
  if (name.includes('taco') || name.includes('quesadilla') || cat.includes('taco')) {
    return [
      'Pechuga y muslo de pollo asado al carbón deshebrado',
      name.includes('quesadilla') ? 'Queso Gouda y Chihuahua 100% de leche de vaca fundido' : 'Tortillas de maíz nixtamalizado',
      'Cebolla blanca picada y cilantro criollo fresco',
      'Limones jugosos',
      'Salsa tatemada en molcajete y chiles toreados',
    ]
  }

  // Ensaladas
  if (name.includes('ensalada') || cat.includes('ensalada')) {
    return [
      'Lechuga romana e italiana fresca desinfectada',
      'Jitomate bola en gajos y pepino persa',
      'Aguacate Hass fresco en rebanadas',
      name.includes('pollo') || name.includes('mixta') ? 'Tiras de pechuga de pollo asada al carbón' : 'Zanahoria rallada y crutones',
      'Aderezo de la casa (vinagreta de mostaza y miel o cilantro ranch)',
    ]
  }

  // Bebidas
  if (cat.includes('bebida') || name.includes('horchata') || name.includes('jamaica') || name.includes('refresco')) {
    if (name.includes('horchata')) {
      return [
        'Arroz blanco selecto remojado artesanalmente',
        'Canela en raja de Ceilán molida',
        'Leche entera pasteurizada y leche condensada',
        'Extracto puro de vainilla de Papantla',
        'Azúcar de caña y agua purificada',
      ]
    }
    if (name.includes('jamaica')) {
      return [
        'Flor de jamaica 100% natural cosechada a mano',
        'Agua purificada filtrada',
        'Azúcar de caña en cantidad moderada',
        'Hielos cristalinos',
      ]
    }
    return [
      'Bebida refrescante gasificada servida bien fría',
      'Agua purificada, azúcares y concentrados de sabor tradicional',
    ]
  }

  // Postres
  if (cat.includes('postre') || name.includes('flan') || name.includes('arroz') || name.includes('pay')) {
    if (name.includes('flan')) {
      return [
        'Huevos frescos de granja',
        'Leche condensada y leche evaporada',
        'Queso crema pasteurizado',
        'Extracto de vainilla natural',
        'Caramelo dorado artesanal a fuego lento',
      ]
    }
    if (name.includes('arroz')) {
      return [
        'Arroz blanco grano largo cocido al dente',
        'Leche entera y leche condensada',
        'Canela en rama y ralladura de cáscara de naranja',
        'Uvas pasas sin semilla',
      ]
    }
    return [
      'Base crocante de galleta María con mantequilla pura',
      'Relleno cremoso de queso Philadelphia y crema fresca',
      'Cobertura de mermelada artesanal de zarzamora silvestre',
    ]
  }

  // Nuggets o Hamburguesas
  if (name.includes('nugget') || name.includes('hamburguesa')) {
    return [
      '100% pechuga de pollo fresca molida',
      'Empanizado crujiente dorado con harina de trigo y especias',
      'Especias aromáticas y sal de mar',
      'Salsa BBQ artesanal o aderezo tártara',
    ]
  }

  // Por defecto
  return [
    'Ingredientes frescos preparados el mismo día',
    'Receta tradicional de la casa Pollo Feliz',
    'Condimentos y especias naturales',
    'Acompañado de salsas y complementos de calidad',
  ]
}

export function getItemAllergens(item) {
  if (!item) return { contains: [], freeOf: [], spiceLevel: 'Suave' }
  if (item.allergens && typeof item.allergens === 'object') {
    return item.allergens
  }

  const name = (item.name || '').toLowerCase()
  const cat = (item.category || '').toLowerCase()

  const contains = []
  const freeOf = []
  let spiceLevel = 'Suave'

  // Gluten check
  if (
    name.includes('hamburguesa') ||
    name.includes('spaguetti') ||
    name.includes('nugget') ||
    name.includes('pay') ||
    name.includes('harina') ||
    name.includes('cruton')
  ) {
    contains.push({ id: 'gluten', name: 'Gluten (Trigo)', icon: '🌾', severity: 'warning' })
  } else {
    freeOf.push({ id: 'gluten_free', name: 'Libre de Gluten (con tortillas de maíz)', icon: '🌾' })
  }

  // Dairy check
  if (
    name.includes('quesadilla') ||
    name.includes('queso') ||
    name.includes('spaguetti') ||
    name.includes('flan') ||
    name.includes('arroz con leche') ||
    name.includes('pay') ||
    name.includes('horchata') ||
    name.includes('mantequilla')
  ) {
    contains.push({ id: 'dairy', name: 'Lácteos (Leche / Queso)', icon: '🥛', severity: 'warning' })
  } else {
    freeOf.push({ id: 'dairy_free', name: 'Sin Lácteos', icon: '🥛' })
  }

  // Eggs check
  if (name.includes('flan') || name.includes('nugget') || name.includes('hamburguesa') || name.includes('pay')) {
    contains.push({ id: 'eggs', name: 'Huevo', icon: '🥚', severity: 'warning' })
  }

  // Soy check
  if (name.includes('nugget') || name.includes('hamburguesa')) {
    contains.push({ id: 'soy', name: 'Soya (Trazas)', icon: '🌱', severity: 'info' })
  }

  // Spice level
  if (name.includes('salsa') || name.includes('toreado') || name.includes('habanero')) {
    spiceLevel = 'Picante 🔥🔥'
  } else if (name.includes('taco') || name.includes('especial') || cat.includes('pollo')) {
    spiceLevel = 'Medio (Salsas al gusto) 🌶️'
  } else {
    spiceLevel = 'No pica (Apto para niños) 😊'
  }

  return {
    contains,
    freeOf,
    spiceLevel,
    crossContaminationWarning: 'Alimentos preparados en cocina donde también se manejan cereales, lácteos y especias.',
  }
}
