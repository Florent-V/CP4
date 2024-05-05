import { startStimulusApp } from '@symfony/stimulus-bundle';
import Clipboard from '@stimulus-components/clipboard'

const app = startStimulusApp();
app.register('clipboard', Clipboard)
// register any custom, 3rd party controllers here
// app.register('some_controller_name', SomeImportedController);
