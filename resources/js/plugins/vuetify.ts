import {createVuetify} from 'vuetify'
import 'vuetify/lib/styles/main.css'
import {ru} from 'vuetify/locale'
import {VDateInput} from 'vuetify/labs/VDateInput'

import {aliases, mdi} from 'vuetify/iconsets/mdi-svg'
import {mdiCastEducation, mdiNewspaper, mdiCamera, mdiPhoneCheckOutline} from '@mdi/js'

aliases['castEducation'] = mdiCastEducation
aliases['newspaper'] = mdiNewspaper
aliases['mdiCamera'] = mdiCamera
aliases['phoneCheckOutline'] = mdiPhoneCheckOutline

export default function initVuetifyPlugin() {
    const vuetify = createVuetify({
        icons: {
            defaultSet: 'mdi',
            aliases,
            sets: {
                mdi
            }
        },
        defaults: {
            VCard: {
                elevation: 5
            },
            VDateInput: {
                placeholder: 'день.месяц.год'
            }
        },
        components: {
            VDateInput,
        },
        locale: {
            locale: 'ru',
            messages: {ru},
        },
        ssr: {
            clientWidth: 1920,
            clientHeight: 1080
        }
    });

    return vuetify;
}
