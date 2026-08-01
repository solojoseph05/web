import PrimeVue from 'primevue/config'

import Aura from '@primeuix/themes/aura'

export default {

    install(app){

        app.use(PrimeVue,{

            theme:{

                preset:Aura

            }

        })

    }

}