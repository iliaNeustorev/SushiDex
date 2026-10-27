import {createVuetify} from 'vuetify'
import 'vuetify/lib/styles/main.css'
import {ru} from 'vuetify/locale'
import {VDateInput} from 'vuetify/labs/VDateInput'

import {aliases, mdi} from 'vuetify/iconsets/mdi-svg'
import {
    mdiCastEducation,
    mdiNewspaper,
    mdiCamera,
    mdiPhoneCheckOutline,
    mdiDeleteOutline,
    mdiMenuRight,
    mdiMenuLeft,
    mdiEye,
    mdiEyeOff,
    mdiDeleteRestore,
    mdiCartOutline,
    mdiCart,
    mdiChevronUp,
    mdiChevronDown,
    mdiCashClock,
    mdiMagnify,
    mdiAlertOutline,
    mdiInformationOutline,
    mdiCheckCircleOutline,
    mdiCloseCircleOutline,
    mdiCogs,
    mdiCheck,
    mdiAccountOutline,
    mdiLogout,
    mdiBellOutline,
    mdiBellRing,
    mdiRead,
    mdiFormatAlignJustify
} from '@mdi/js'

aliases['castEducation'] = mdiCastEducation
aliases['newspaper'] = mdiNewspaper
aliases['mdiCamera'] = mdiCamera
aliases['phoneCheckOutline'] = mdiPhoneCheckOutline
aliases['deleteOutline'] = mdiDeleteOutline
aliases['menuRight'] = mdiMenuRight
aliases['menuLeft'] = mdiMenuLeft
aliases['mdiEye'] = mdiEye
aliases['mdiEyeOff'] = mdiEyeOff
aliases['mdiDeleteRestore'] = mdiDeleteRestore
aliases['mdiCartOutline'] = mdiCartOutline
aliases['mdiCart'] = mdiCart
aliases['mdiUp'] = mdiChevronUp
aliases['mdiDown'] = mdiChevronDown
aliases['mdiCashClock'] = mdiCashClock
aliases['mdiMagnify'] = mdiMagnify
aliases['mdiAlertOutline'] = mdiAlertOutline
aliases['mdiInformationOutline'] = mdiInformationOutline
aliases['mdiCheckOutline'] = mdiCheckCircleOutline
aliases['mdiCloseOutline'] = mdiCloseCircleOutline
aliases['mdiCogs'] = mdiCogs
aliases['mdiCheck'] = mdiCheck
aliases['accountOutline'] = mdiAccountOutline
aliases['logout'] = mdiLogout
aliases['bellOutline'] = mdiBellOutline
aliases['bellRing'] = mdiBellRing
aliases['read'] = mdiRead
aliases['mdiFormatAlignJustify'] = mdiFormatAlignJustify


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
