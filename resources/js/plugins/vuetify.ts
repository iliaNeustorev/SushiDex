import {createVuetify} from 'vuetify'
import 'vuetify/lib/styles/main.css'

import {VDateInput} from 'vuetify/labs/VDateInput'

import {aliases, mdi} from 'vuetify/iconsets/mdi-svg'
import {mdiCastEducation, mdiNewspaper, mdiCamera} from '@mdi/js'

aliases['castEducation'] = mdiCastEducation
aliases['newspaper'] = mdiNewspaper
aliases['mdiCamera'] = mdiCamera

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
            }
        },
        components: {
            VDateInput,
        }
    });

    return vuetify;
}
